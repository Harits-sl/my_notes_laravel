<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi mass assignment
     */
    protected $fillable = [
        'name',
    ];

    public function notes()
    {
        return $this->belongsToMany(
            Note::class,
            'notes_category'
        )->withTimestamps();
    }
}
