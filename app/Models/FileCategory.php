<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileCategory extends Model
{
    protected $table = 'file_categories';
    protected $primaryKey = 'file_category_id';
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    public function memberClubFiles()
    {
        return $this->hasMany(MemberClubFile::class, 'file_category_id');
    }

    public function eventFiles()
    {
        return $this->hasMany(EventFile::class, 'file_category_id');
    }

    public function clubFiles()
    {
        return $this->hasMany(ClubFile::class, 'file_category_id');
    }
}
