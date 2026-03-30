<?php

namespace App\Http\Controllers;

use App\Constants\FileMessages;
use App\Http\Requests\FileUploadRequest;
use App\Models\Club;
use App\Models\Event;
use App\Models\File;
use App\Models\MemberClub;
use App\Services\FileService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Throwable;

class FileController extends Controller
{
    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Upload file for a model (event, club, member_club)
     */
    public function upload(FileUploadRequest $request, string $modelType, int $modelId): JsonResponse
    {
        try {
            $model = $this->getModel($modelType, $modelId);

            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => FileMessages::MODEL_NOT_FOUND,
                ], 404);
            }

            $this->authorize('uploadTo', $model);

            $validated = $request->validated();

            $file = $this->fileService->storeFile(
                $request->file('file'),
                $request->user()->user_id
            );

            $this->attachFileToModel(
                $model,
                $file,
                (int) $validated['file_category_id'],
                $modelType
            );

            return response()->json([
                'success' => true,
                'message' => FileMessages::FILE_UPLOADED,
                'file' => $file,
            ], 201);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => FileMessages::UPLOAD_ERROR,
            ], 500);
        }
    }

    /**
     * Get all files for a model
     */
    public function list(string $modelType, int $modelId): JsonResponse
    {
        try {
            $model = $this->getModel($modelType, $modelId);

            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => FileMessages::MODEL_NOT_FOUND,
                ], 404);
            }

            $this->authorize('viewAny', File::class);

            $files = $this->getFilesForModel($model, $modelType);

            return response()->json([
                'success' => true,
                'files' => $files->map(function (File $file) {
                    return [
                        'file_id' => $file->file_id,
                        'file_name' => $file->file_name,
                        'file_size' => $file->file_size,
                        'file_type' => $file->file_type,
                        'file_category_id' => $file->pivot->file_category_id,
                        'created_at' => $file->pivot->created_at,
                        'url' => $this->fileService->getDownloadUrl($file),
                    ];
                }),
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => FileMessages::LIST_ERROR,
            ], 500);
        }
    }

    /**
     * Get files of specific category for a model
     */
    public function listByCategory(string $modelType, int $modelId, int $fileCategoryId): JsonResponse
    {
        try {
            $model = $this->getModel($modelType, $modelId);

            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => FileMessages::MODEL_NOT_FOUND,
                ], 404);
            }

            $this->authorize('viewAny', File::class);

            $files = $this->getFilesForModel($model, $modelType, $fileCategoryId);

            return response()->json([
                'success' => true,
                'file_category_id' => $fileCategoryId,
                'files' => $files->map(function (File $file) {
                    return [
                        'file_id' => $file->file_id,
                        'file_name' => $file->file_name,
                        'file_size' => $file->file_size,
                        'file_type' => $file->file_type,
                        'file_category_id' => $file->pivot->file_category_id,
                        'created_at' => $file->pivot->created_at,
                        'url' => $this->fileService->getDownloadUrl($file),
                    ];
                }),
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => FileMessages::LIST_ERROR,
            ], 500);
        }
    }

    /**
     * Delete a file from a model
     */
    public function delete(string $modelType, int $modelId, int $fileId): JsonResponse
    {
        try {
            $model = $this->getModel($modelType, $modelId);

            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => FileMessages::MODEL_NOT_FOUND,
                ], 404);
            }

            $file = File::find($fileId);

            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => FileMessages::FILE_NOT_FOUND,
                ], 404);
            }

            if (!$this->modelHasFile($model, $fileId, $modelType)) {
                return response()->json([
                    'success' => false,
                    'message' => FileMessages::FILE_NOT_FOUND,
                ], 404);
            }

            $this->authorize('delete', $file);

            $this->detachFileFromModel($model, $file, $modelType);

            $otherUsages = $this->countFileUsages($file);

            if ($otherUsages === 0) {
                $this->fileService->deleteFile($file);
            }

            return response()->json([
                'success' => true,
                'message' => FileMessages::FILE_DELETED,
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => FileMessages::DELETE_ERROR,
            ], 500);
        }
    }

    /**
     * Download a file
     */
    public function download(File $file,  Request $request)
    {
        $this->authorize('view', $file);

        $path = storage_path('app/private/' . $file->file_path);

        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        if (str_starts_with($file->file_type, 'image/') && !$request->boolean('download')) {
            return response()->file($path, [
                'Content-Type' => $file->file_type,
            ]);
        }

        return response()->download($path, $file->file_name);
    }

    /**
     * Get model instance by type and ID
     */
    private function getModel(string $modelType, int $modelId): Event|Club|MemberClub|null
    {
        return match ($modelType) {
            'event' => Event::find($modelId),
            'club' => Club::find($modelId),
            'member_club' => MemberClub::find($modelId),
            default => null,
        };
    }

    /**
     * Attach file to model using appropriate relationship
     */
    private function attachFileToModel(Event|Club|MemberClub $model, File $file, int $fileCategoryId, string $modelType): void
    {
        match ($modelType) {
            'event' => $model->eventFiles()->attach($file->file_id, [
                'file_category_id' => $fileCategoryId,
            ]),
            'club' => $model->clubFiles()->attach($file->file_id, [
                'file_category_id' => $fileCategoryId,
            ]),
            'member_club' => $model->memberClubFiles()->attach($file->file_id, [
                'file_category_id' => $fileCategoryId,
            ]),
        };
    }

    /**
     * Detach file from model using appropriate relationship
     */
    private function detachFileFromModel(Event|Club|MemberClub $model, File $file, string $modelType): void
    {
        match ($modelType) {
            'event' => $model->eventFiles()->detach($file->file_id),
            'club' => $model->clubFiles()->detach($file->file_id),
            'member_club' => $model->memberClubFiles()->detach($file->file_id),
        };
    }

    /**
     * Get files for a model, optionally filtered by category ID
     */
    private function getFilesForModel(Event|Club|MemberClub $model, string $modelType, ?int $fileCategoryId = null): Collection
    {
        $files = match ($modelType) {
            'event' => $model->eventFiles(),
            'club' => $model->clubFiles(),
            'member_club' => $model->memberClubFiles(),
        };

        if ($fileCategoryId !== null) {
            $files->wherePivot('file_category_id', $fileCategoryId);
        }

        return $files->get();
    }

    /**
     * Check whether given model has the given file attached
     */
    private function modelHasFile(Event|Club|MemberClub $model, int $fileId, string $modelType): bool
    {
        return match ($modelType) {
            'event' => $model->eventFiles()->where('files.file_id', $fileId)->exists(),
            'club' => $model->clubFiles()->where('files.file_id', $fileId)->exists(),
            'member_club' => $model->memberClubFiles()->where('files.file_id', $fileId)->exists(),
        };
    }

    /**
     * Count how many models still use a file
     */
    private function countFileUsages(File $file): int
    {
        return
            $file->clubs()->count() +
            $file->events()->count() +
            $file->memberClubs()->count();
    }

    public function webDelete(string $modelType, int $modelId, int $fileId)
    {
        $model = $this->getModel($modelType, $modelId);

        abort_if(!$model, 404);

        $file = File::findOrFail($fileId);

        abort_unless($this->modelHasFile($model, $fileId, $modelType), 404);

        $this->authorize('delete', $file);

        $this->detachFileFromModel($model, $file, $modelType);

        $otherUsages = $this->countFileUsages($file);

        if ($otherUsages === 0) {
            $this->fileService->deleteFile($file);
        }

        return redirect()->back()->with('success', 'File deleted successfully.');
    }
}