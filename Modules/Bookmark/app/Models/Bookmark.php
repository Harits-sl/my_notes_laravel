<?php

namespace Modules\Bookmark\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Bookmark extends Model
{
    protected $table = 'notes';

    protected $fillable = [
        'url',
        'title',
        'desc',
        'image',
        'provider_name'
    ];

    protected $appends = ['image_url'];

    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'notes_category',
            'note_id',
            'category_id'
        )->withTimestamps();
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->image
                ? url(Storage::url('images/' . $this->image))
                : null
        );
    }
}
