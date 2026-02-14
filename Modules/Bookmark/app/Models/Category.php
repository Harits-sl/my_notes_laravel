<?php

namespace Modules\Bookmark\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
    ];

    public function bookmarks()
    {
        return $this->belongsToMany(
            Bookmark::class,
            'notes_category',
            'category_id',
            'note_id'
        )->withTimestamps();
    }
}
