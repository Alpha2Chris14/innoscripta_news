<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'source' => [
                'id' => $this->source->id,
                'name' => $this->source->name,
                'slug' => $this->source->slug
            ],
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'author' => $this->author,
            'url' => $this->url,
            'image_url' => $this->image_url,
            'category' => $this->category,
            'published_at' => optional($this->published_at)->toIso8601String(),
            'meta' => $this->meta,
        ];
    }
}
