<?php

namespace App\Services;

use App\Models\File;
use App\Constants\FileMessages;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    // File categories
    const CATEGORY_LOGO = 'logo';
    const CATEGORY_DOCUMENT = 'document';
    const CATEGORY_PHOTO = 'photo';
    const CATEGORY_OTHER = 'other';

    // Allowed categories
    const ALLOWED_CATEGORIES = [
        self::CATEGORY_LOGO,
        self::CATEGORY_DOCUMENT,
        self::CATEGORY_PHOTO,
        self::CATEGORY_OTHER,
    ];

    // File limits (in bytes)
    const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10 MB
    const MAX_IMAGE_SIZE = 5 * 1024 * 1024; // 5 MB

    // Allowed MIME types
    const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    /**
     * Store an uploaded file and create a File record.
     *
     * @param UploadedFile $uploadedFile
     * @param string|null $disk
     * @return File
     */
    public function storeFile(UploadedFile $uploadedFile, ?string $disk = 'local'): File
    {
        // Create unique filename
        $filename = $this->generateUniqueFilename($uploadedFile);
        
        // Store file on disk
        $path = $uploadedFile->storeAs('files', $filename, $disk);

        // Create File record in database
        $file = File::create([
            'file_name' => $uploadedFile->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $uploadedFile->getClientMimeType(),
            'file_size' => $uploadedFile->getSize(),
        ]);

        return $file;
    }

    /**
     * Delete a file and its record.
     *
     * @param File $file
     * @param string|null $disk
     * @return bool
     */
    public function deleteFile(File $file, ?string $disk = 'local'): bool
    {
        // Delete physical file from storage
        if (Storage::disk($disk)->exists($file->file_path)) {
            Storage::disk($disk)->delete($file->file_path);
        }

        // Delete file relations
        $file->fileRelations()->delete();

        // Delete file record (soft delete)
        return $file->delete();
    }

    /**
     * Force delete a file (hard delete).
     *
     * @param File $file
     * @param string|null $disk
     * @return bool
     */
    public function forceDeleteFile(File $file, ?string $disk = 'local'): bool
    {
        // Delete physical file from storage
        if (Storage::disk($disk)->exists($file->file_path)) {
            Storage::disk($disk)->delete($file->file_path);
        }

        // Delete file relations
        $file->fileRelations()->delete();

        // Force delete file record
        return $file->forceDelete();
    }

    /**
     * Get the URL to download a file.
     *
     * @param File $file
     * @return string
     */
    public function getDownloadUrl(File $file): string
    {
        return route('files.download', $file->id);
    }

    /**
     * Get file contents.
     *
     * @param File $file
     * @param string|null $disk
     * @return string
     */
    public function getFileContents(File $file, ?string $disk = 'local'): string
    {
        return Storage::disk($disk)->get($file->file_path);
    }

    /**
     * Validate a file before upload.
     *
     * @param UploadedFile $file
     * @param string $category
     * @return array ['valid' => bool, 'message' => string|null]
     */
    public function validateFile(UploadedFile $file, string $category = 'document'): array
    {
        // Check if category is allowed
        if (!in_array($category, self::ALLOWED_CATEGORIES)) {
            return [
                'valid' => false,
                'message' => FileMessages::CATEGORY_NOT_ALLOWED,
            ];
        }

        // Check MIME type
        if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
            return [
                'valid' => false,
                'message' => FileMessages::FILE_TYPE_NOT_ALLOWED,
            ];
        }

        // Check file size
        $maxSize = $this->getMaxSizeForCategory($category);
        if ($file->getSize() > $maxSize) {
            return [
                'valid' => false,
                'message' => FileMessages::FILE_TOO_LARGE,
            ];
        }

        return ['valid' => true, 'message' => null];
    }

    /**
     * Generate unique filename to avoid conflicts.
     *
     * @param UploadedFile $file
     * @return string
     */
    private function generateUniqueFilename(UploadedFile $file): string
    {
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->timestamp;
        $random = random_int(1000, 9999);

        return "{$name}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Get maximum file size for a category.
     *
     * @param string $category
     * @return int
     */
    private function getMaxSizeForCategory(string $category): int
    {
        return in_array($category, [self::CATEGORY_LOGO, self::CATEGORY_PHOTO])
            ? self::MAX_IMAGE_SIZE
            : self::MAX_FILE_SIZE;
    }

    /**
     * Format bytes to human readable format.
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
