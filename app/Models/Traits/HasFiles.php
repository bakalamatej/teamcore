<?php

namespace App\Models\Traits;

use App\Models\File;
use App\Models\FileRelation;

trait HasFiles
{
    /**
     * Get all file relations for this model (polymorphic).
     */
    public function fileRelations()
    {
        return $this->morphMany(FileRelation::class, 'fileable');
    }

    /**
     * Get all files associated with this model through FileRelation.
     */
    public function files()
    {
        return $this->hasManyThrough(
            File::class,
            FileRelation::class,
            'fileable_id',
            'id',
            $this->getKeyName(),
            'file_id'
        )->where('file_relations.fileable_type', static::class);
    }

    /**
     * Get files of a specific category (logo, document, photo, training_plan, etc.)
     *
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filesByCategory(string $category)
    {
        return $this->fileRelations()
                    ->where('file_category', $category)
                    ->with('file')
                    ->get()
                    ->pluck('file');
    }

    /**
     * Attach a file to this model with a category.
     *
     * @param File $file
     * @param string $category
     * @return FileRelation
     */
    public function attachFile(File $file, string $category = 'document')
    {
        return FileRelation::create([
            'file_id' => $file->id,
            'fileable_type' => static::class,
            'fileable_id' => $this->id,
            'file_category' => $category,
        ]);
    }

    /**
     * Detach a file from this model.
     *
     * @param File|int $file
     * @return int
     */
    public function detachFile($file)
    {
        $fileId = $file instanceof File ? $file->id : $file;
        
        return $this->fileRelations()
                    ->where('file_id', $fileId)
                    ->delete();
    }

    /**
     * Check if this model has files of a specific category.
     *
     * @param string $category
     * @return bool
     */
    public function hasFileCategory(string $category): bool
    {
        return $this->fileRelations()
                    ->where('file_category', $category)
                    ->exists();
    }
}
