<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ArticleController extends Controller

{

    public function index(Request $request)
    {
        $allowed = ['q', 'source', 'category', 'author', 'date_from', 'date_to', 'per_page', 'page'];
        $params = array_filter($request->only($allowed), fn($v) => $v !== null && $v !== '');

        $perPage = (int)($params['per_page'] ?? 15);
        $page = (int)($params['page'] ?? 1);

        // stable cache key based on only relevant query params
        $cacheKey = 'articles:' . md5(http_build_query($params));

        $query = $this->buildArticlesQuery($params)->orderBy('published_at', 'desc');

        $results = Cache::remember($cacheKey, 60, function () use ($query, $perPage, $page) {
            return $query->paginate($perPage, ['*'], 'page', $page);
        });

        return ArticleResource::collection($results);
    }

    /**
     * Build the base query for listing articles from filter params.
     */
    private function buildArticlesQuery(array $params)
    {
        $query = Article::with('source');

        if (!empty($params['q'])) {
            $q = $params['q'];
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            });
        }

        if (!empty($params['source'])) {
            $source = $params['source'];
            $query->whereHas('source', function ($s) use ($source) {
                $s->where('slug', $source);
            });
        }

        if (!empty($params['category'])) {
            $query->where('category', $params['category']);
        }

        if (!empty($params['author'])) {
            $query->where('author', 'like', "%{$params['author']}%");
        }

        if (!empty($params['date_from'])) {
            $query->where('published_at', '>=', $params['date_from']);
        }

        if (!empty($params['date_to'])) {
            $query->where('published_at', '<=', $params['date_to']);
        }

        return $query;
    }

    /**
     * Display the specified article.
     */
    public function show($id)
    {
        $article = Article::with('source')->findOrFail($id);
        return new ArticleResource($article);
    }

    /**
     * fetches all the unique category names from the articles table, ignoring any null ones.
     */
    public function categories()
    {
        $categories = Article::select('category')->whereNotNull('category')->distinct()->pluck('category');
        return response()->json($categories);
    }
}
