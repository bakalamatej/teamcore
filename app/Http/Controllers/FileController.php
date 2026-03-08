<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Club;
use App\Models\File;
use App\Models\MemberClub;
use App\Http\Requests\FileUploadRequest;
use App\Services\FileService;
use App\Constants\FileMessages;
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

            // Store file
            $file = $this->fileService->storeFile(
                $request->file('file')
            );

            // Attach file to model using appropriate relationship
            $this->attachFileToModel($model, $file, $request->validated()['category'], $modelType);

            return response()->json([
                'success' => true,
                'message' => FileMessages::FILE_UPLOADED,
                'file' => $file
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => FileMessages::UPLOAD_ERROR
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
                'files' => $files->map(function ($file) {
                    return [
                        'file_id'    => $file->file_id,
                        'file_name'  => $file->file_name,
                        'file_size'  => $file->file_size,
                        'file_type'  => $file->file_type,
                        'category'   => $file->pivot->file_category_id,
                        'created_at' => $file->pivot->created_at,
                        'url'        => $this->fileService->getDownloadUrl($file),
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => FileMessages::LIST_ERROR
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

            // Authorize file viewing
            $this->authorize('viewAny', File::class);

            $files = $this->getFilesForModel($model, $modelType, $category);

            return response()->json([
                'success' => true,
                'category' => $category,
                'files' => $files->map(fn($file) => [
                    'file_id'   => $file->file_id,
                    'file_name' => $file->file_name,
                    'file_size' => $file->file_size,
                    'file_type' => $file->file_type,
                    'url'       => $this->fileService->getDownloadUrl($file),
                ])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => FileMessages::LIST_ERROR
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
                'message' => FileMessages::DELETE_ERROR
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
        $file = File::findOrFail($fileId);
        $this->authorize('view', $file);
        $path = storage_path('app/private/' . $file->file_path);
        return response()->download($path, $file->file_name);
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
            'event'       => Event::find($modelId),
            'club'        => Club::find($modelId),
            'member_club' => MemberClub::find($modelId),
            default       => null,
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
            'event'       => $model->eventFiles()->attach($file->file_id, ['file_category_id' => $category]),
            'club'        => $model->clubFiles()->attach($file->file_id, ['file_category_id' => $category]),
            'member_club' => $model->memberClubFiles()->attach($file->file_id, ['file_category_id' => $category]),
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
            'event'       => $model->eventFiles()->detach($file->file_id),
            'club'        => $model->clubFiles()->detach($file->file_id),
            'member_club' => $model->memberClubFiles()->detach($file->file_id),
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
            'event'       => $model->eventFiles(),
            'club'        => $model->clubFiles(),
            'member_club' => $model->memberClubFiles(),
        };

        if ($category) {
            $files = $files->wherePivot('file_category_id', $category);
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
