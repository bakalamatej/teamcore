<?php

namespace App\Models\Traits;

use App\Models\File;

trait HasFiles
{
    /**
     * Polymorphic relationship: returns all files associated with this model.
     */
    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    /**
     * Attach a file to this model.
     *
     * @param File $file
     * @return void
     */
    public function attachFile(File $file)
    {
        $this->files()->save($file);
    }

    /**
     * Detach a file from this model.
     *
     * @param File $file
     * @return void
     */
    public function detachFile(File $file)
    {
        $this->files()->where('id', $file->id)->delete();
    }

    /**
     * Get files of a specific type.
     *
     * @param string|null $type
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function filesByType(?string $type = null)
    {
        $query = $this->files();
        if ($type) {
            $query->where('file_type', $type);
        }
        return $query;
    }
}
