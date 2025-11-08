<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $fillable = ['name', 'slug', 'config', 'enabled'];
    protected $casts = ['config' => 'array', 'enabled' => 'boolean'];

    /**
     * Get the articles for the source. A source can have many articles.
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
