<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileCategory extends Model
{
    protected $table = 'file_categories';
    protected $primaryKey = 'category_id';
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    public function memberClubFiles()
    {
        return $this->hasMany(MemberClubFile::class, 'category_id');
    }

    public function eventFiles()
    {
        return $this->hasMany(EventFile::class, 'category_id');
    }

    public function clubFiles()
    {
        return $this->hasMany(ClubFile::class, 'category_id');
    }
}
