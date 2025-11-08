<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'source_id',
        'external_id',
        'title',
        'description',
        'content',
        'author',
        'url',
        'image_url',
        'category',
        'language',
        'published_at',
        'meta'
    ];

    protected $casts = ['meta' => 'array', 'published_at' => 'datetime'];

    /**
     * Get the source that owns the article. An article belongs to a source.
     */
    public function source()
    {
        return $this->belongsTo(Source::class);
    }
}
