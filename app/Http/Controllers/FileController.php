<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Club;
use App\Models\File;
use App\Http\Requests\FileUploadRequest;
use App\Services\FileService;
use App\Constants\FileMessages;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FileController extends Controller
{
    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Upload file for a model (Event, Club, MemberClub)
     *
     * @param FileUploadRequest $request
     * @param string $modelType (event, club, member_club)
     * @param int $modelId
     * @return JsonResponse
     */
    public function upload(FileUploadRequest $request, string $modelType, int $modelId): JsonResponse
    {
        try {
            // Get the model instance
            $model = $this->getModel($modelType, $modelId);
            
            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => FileMessages::MODEL_NOT_FOUND
                ], 404);
            }

            // Authorize file upload
            $this->authorize('create', File::class);

            // Validate file
            $validation = $this->fileService->validateFile(
                $request->file('file'),
                $request->input('category')
            );

            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $validation['message']
                ], 422);
            }

            // Store file
            $file = $this->fileService->storeFile(
                $request->file('file')
            );

            // Attach file to model using appropriate relationship
            $this->attachFileToModel($model, $file, $request->input('category'), $modelType);

            return response()->json([
                'success' => true,
                'message' => FileMessages::FILE_UPLOADED,
                'file' => $file
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => FileMessages::UPLOAD_ERROR . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all files for a model (Event, Club, MemberClub)
     *
     * @param string $modelType (event, club, member_club)
     * @param int $modelId
     * @return JsonResponse
     */
    public function list(string $modelType, int $modelId): JsonResponse
    {
        try {
            $model = $this->getModel($modelType, $modelId);
            
            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => FileMessages::MODEL_NOT_FOUND
                ], 404);
            }

            // Authorize file viewing
            $this->authorize('viewAny', File::class);

            $files = $this->getFilesForModel($model, $modelType);

            return response()->json([
                'success' => true,
                'files' => $files->map(function ($item) {
                    // Pivot table result
                    $file = $item->file;
                    $category = $item->pivot->file_category;
                    $created_at = $item->pivot->created_at;

                    return [
                        'file_id' => $file->id,
                        'file_name' => $file->file_name,
                        'file_size' => $file->file_size,
                        'file_type' => $file->file_type,
                        'category' => $category,
                        'created_at' => $created_at,
                        'url' => $this->fileService->getDownloadUrl($file),
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => FileMessages::LIST_ERROR . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get files of specific category for a model
     *
     * @param string $modelType (event, club, member_club)
     * @param int $modelId
     * @param string $category
     * @return JsonResponse
     */
    public function listByCategory(string $modelType, int $modelId, string $category): JsonResponse
    {
        try {
            $model = $this->getModel($modelType, $modelId);
            
            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => FileMessages::MODEL_NOT_FOUND
                ], 404);
            }

            $files = $this->getFilesForModel($model, $modelType, $category);

            return response()->json([
                'success' => true,
                'category' => $category,
                'files' => $files->map(function ($item) use ($modelType) {
                    if ($modelType === 'member') {
                        $file = $item->file;
                    } else {
                        $file = $item->file;
                    }

                    return [
                        'id' => $file->id,
                        'file_name' => $file->file_name,
                        'file_size' => $file->file_size,
                        'file_type' => $file->file_type,
                        'url' => $this->fileService->getDownloadUrl($file),
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => FileMessages::LIST_ERROR . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a file from a model
     *
     * @param string $modelType (event, club, member_club)
     * @param int $modelId
     * @param int $fileId
     * @return JsonResponse
     */
    public function delete(string $modelType, int $modelId, int $fileId): JsonResponse
    {
        try {
            $model = $this->getModel($modelType, $modelId);
            
            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => FileMessages::MODEL_NOT_FOUND
                ], 404);
            }

            $file = File::find($fileId);
            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => FileMessages::FILE_NOT_FOUND
                ], 404);
            }

            // Authorize file deletion
            $this->authorize('delete', $file);

            // Detach file from model using appropriate relationship
            $this->detachFileFromModel($model, $file, $modelType);

            // Check if this file is used by other models
            $otherUsages = $this->countFileUsages($file);

            // If no other usages, delete the file
            if ($otherUsages === 0) {
                $this->fileService->deleteFile($file);
            }

            return response()->json([
                'success' => true,
                'message' => FileMessages::FILE_DELETED
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => FileMessages::DELETE_ERROR . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a file
     *
     * @param int $fileId
     * @return mixed
     */
    public function download(int $fileId)
    {
        try {
            $file = File::findOrFail($fileId);
            $path = storage_path('app/private/' . $file->file_path);
            return response()->download($path, $file->file_name);
        } catch (\Exception $e) {
            abort(404, FileMessages::FILE_NOT_FOUND);
        }
    }

    /**
     * Get model instance by type and ID
     *
     * @param string $modelType
     * @param int $modelId
     * @return mixed
     */
    private function getModel(string $modelType, int $modelId)
    {
        return match ($modelType) {
            'event' => Event::find($modelId),
            'club' => Club::find($modelId),
            'member_club' => \App\Models\MemberClub::find($modelId),
            default => null,
        };
    }

    /**
     * Attach file to model using appropriate relationship
     *
     * @param mixed $model
     * @param File $file
     * @param string $category
     * @param string $modelType
     */
    private function attachFileToModel($model, File $file, string $category, string $modelType): void
    {
        match ($modelType) {
            'event' => $model->eventFiles()->attach($file->id, ['file_category' => $category]),
            'club' => $model->clubFiles()->attach($file->id, ['file_category' => $category]),
            'member_club' => $model->memberClubFiles()->attach($file->id, ['file_category' => $category]),
        };
    }

    /**
     * Detach file from model using appropriate relationship
     *
     * @param mixed $model
     * @param File $file
     * @param string $modelType
     */
    private function detachFileFromModel($model, File $file, string $modelType): void
    {
        match ($modelType) {
            'event' => $model->eventFiles()->detach($file->id),
            'club' => $model->clubFiles()->detach($file->id),
            'member_club' => $model->memberClubFiles()->detach($file->id),
        };
    }

    /**
     * Get files for a model, filtered by category if provided
     *
     * @param mixed $model
     * @param string $modelType
     * @param string|null $category
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getFilesForModel($model, string $modelType, ?string $category = null)
    {
        $files = match ($modelType) {
            'event' => $model->eventFiles()->with('file'),
            'club' => $model->clubFiles()->with('file'),
            'member_club' => $model->memberClubFiles()->with('file'),
        };

        if ($category) {
            $files = $files->wherePivot('file_category', $category);
        }

        return $files->get();
    }

    /**
     * Count how many models use a file
     *
     * @param File $file
     * @return int
     */
    private function countFileUsages(File $file): int
    {
        $count = 0;
        $count += $file->clubs()->count();
        $count += $file->events()->count();
        $count += $file->memberClubs()->count();
        return $count;
    }
}
