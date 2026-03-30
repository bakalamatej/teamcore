<?php

namespace App\Services;

use App\Models\File;
use App\Constants\FileMessages;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    const CATEGORY_LOGO = 'logo';
    const CATEGORY_DOCUMENT = 'document';
    const CATEGORY_PHOTO = 'photo';
    const CATEGORY_OTHER = 'other';

    const ALLOWED_CATEGORIES = [
        self::CATEGORY_LOGO,
        self::CATEGORY_DOCUMENT,
        self::CATEGORY_PHOTO,
        self::CATEGORY_OTHER,
    ];

    const MAX_FILE_SIZE = 10 * 1024 * 1024;
    const MAX_IMAGE_SIZE = 5 * 1024 * 1024;

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

    public function storeFile(UploadedFile $uploadedFile, int $uploadedByUserId, ?string $disk = 'local'): File
    {
        $filename = $this->generateUniqueFilename($uploadedFile);

        $path = $uploadedFile->storeAs('files', $filename, $disk);

        return File::create([
            'file_name' => $uploadedFile->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $uploadedFile->getClientMimeType(),
            'file_size' => $uploadedFile->getSize(),
            'uploaded_by_user_id' => $uploadedByUserId,
        ]);
    }

    public function deleteFile(File $file, ?string $disk = 'local'): bool
    {
        $file->clubs()->detach();
        $file->events()->detach();
        $file->memberClubs()->detach();

        return $file->delete();
    }

    public function forceDeleteFile(File $file, ?string $disk = 'local'): bool
    {
        if (Storage::disk($disk)->exists($file->file_path)) {
            Storage::disk($disk)->delete($file->file_path);
        }

        $file->clubs()->detach();
        $file->events()->detach();
        $file->memberClubs()->detach();

        return $file->forceDelete();
    }

    public function getDownloadUrl(File $file): string
    {
        return route('files.download', $file->file_id);
    }

    public function getFileContents(File $file, ?string $disk = 'local'): string
    {
        return Storage::disk($disk)->get($file->file_path);
    }

    public function validateFile(UploadedFile $file, string $category = 'document'): array
    {
        if (!in_array($category, self::ALLOWED_CATEGORIES)) {
            return [
                'valid' => false,
                'message' => FileMessages::CATEGORY_NOT_ALLOWED,
            ];
        }

        if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
            return [
                'valid' => false,
                'message' => FileMessages::FILE_TYPE_NOT_ALLOWED,
            ];
        }

        $maxSize = $this->getMaxSizeForCategory($category);
        if ($file->getSize() > $maxSize) {
            return [
                'valid' => false,
                'message' => FileMessages::FILE_TOO_LARGE,
            ];
        }

        return ['valid' => true, 'message' => null];
    }

    private function generateUniqueFilename(UploadedFile $file): string
    {
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->timestamp;
        $random = random_int(1000, 9999);

        return "{$name}_{$timestamp}_{$random}.{$extension}";
    }

    private function getMaxSizeForCategory(string $category): int
    {
        return in_array($category, [self::CATEGORY_LOGO, self::CATEGORY_PHOTO])
            ? self::MAX_IMAGE_SIZE
            : self::MAX_FILE_SIZE;
    }
}