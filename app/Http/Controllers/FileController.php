<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Member;
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
     * Upload file for a model (Event, Member, Club)
     *
     * @param FileUploadRequest $request
     * @param string $modelType (event, member, club)
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

            // Attach file to model
            $model->attachFile($file, $request->input('category'));

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
     * Get all files for a model (Event, Member, Club)
     *
     * @param string $modelType (event, member, club)
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

            $fileRelations = $model->fileRelations()
                                   ->with('file')
                                   ->get();

            return response()->json([
                'success' => true,
                'files' => $fileRelations->map(function ($relation) {
                    return [
                        'id' => $relation->id,
                        'file_id' => $relation->file_id,
                        'file_name' => $relation->file->file_name,
                        'file_size' => $relation->file->file_size,
                        'file_type' => $relation->file->file_type,
                        'category' => $relation->file_category,
                        'created_at' => $relation->created_at,
                        'url' => $this->fileService->getDownloadUrl($relation->file),
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
     * @param string $modelType (event, member, club)
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

            $files = $model->filesByCategory($category);

            return response()->json([
                'success' => true,
                'category' => $category,
                'files' => $files->map(function ($file) {
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
     * @param string $modelType (event, member, club)
     * @param int $modelId
     * @param int $fileRelationId
     * @return JsonResponse
     */
    public function delete(string $modelType, int $modelId, int $fileRelationId): JsonResponse
    {
        try {
            $model = $this->getModel($modelType, $modelId);
            
            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => FileMessages::MODEL_NOT_FOUND
                ], 404);
            }

            // Find and verify the file relation belongs to this model
            $fileRelation = $model->fileRelations()
                                 ->where('id', $fileRelationId)
                                 ->first();

            if (!$fileRelation) {
                return response()->json([
                    'success' => false,
                    'message' => FileMessages::FILE_NOT_FOUND
                ], 404);
            }

            // Get the file before deleting relation
            $file = $fileRelation->file;

            // Delete the relation
            $fileRelation->delete();

            // Check if this file is used by other models
            $otherRelations = $file->fileRelations()->count();

            // If no other relations, delete the file
            if ($otherRelations === 0) {
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
            'member' => Member::find($modelId),
            'club' => Club::find($modelId),
            default => null,
        };
    }
}
