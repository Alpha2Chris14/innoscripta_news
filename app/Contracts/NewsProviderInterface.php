<?php

namespace App\Contracts;

interface NewsProviderInterface
{
    /*
    Fetch latest articles (should be paginated or limited).
    Return an array of normalized articles:
    [
        'external_id' => ?string,
        'title' => string,
        'description' => string|null,
        'content' => string|null,
        'author' => string|null,
        'url' => string,
        'image_url' => string|null,
        'category' => string|null,
        'language' => string|null,
        'published_at' => string|DateTime|null,
        'meta' => array // raw provider payload
      ]
    */

    /*
     Fetch Latest Articles
     */
    public function fetchLatest(array $options = []): array;
}
