<?php

namespace App\Repositories;

use App\Models\Article;
use Illuminate\Support\Arr;

class ArticleRepository
{
    public function upsertArticles(array $articles, int $sourceId): void
    {
        foreach ($articles as $a) {
            /* normalize payload */
            $record = [
                'source_id' => $sourceId,
                'external_id' => $a['external_id'] ?? null,
                'title' => $a['title'] ?? null,
                'description' => $a['description'] ?? null,
                'content' => $a['content'] ?? null,
                'author' => $a['author'] ?? null,
                'url' => $a['url'] ?? null,
                'image_url' => $a['image_url'] ?? null,
                'category' => $a['category'] ?? null,
                'language' => $a['language'] ?? null,
                'published_at' => $a['published_at'] ?? null,
                'meta' => $a['meta'] ?? null,
            ];

            /* Try upsert by source+external_id if provided, else by url */
            if (!empty($record['external_id'])) {
                Article::updateOrCreate(
                    ['source_id' => $sourceId, 'external_id' => $record['external_id']],
                    $record
                );
            } else {
                Article::updateOrCreate(
                    ['url' => $record['url']],
                    $record
                );
            }
        }
    }
}
