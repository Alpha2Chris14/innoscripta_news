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
        $q = $request->query('q');
        $source = $request->query('source');
        $category = $request->query('category');
        $author = $request->query('author');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $perPage = (int)$request->query('per_page', 15);
        $page = (int)$request->query('page', 1);

        $cacheKey = 'articles:' . md5(serialize($request->query->all()));

        $query = Article::with('source')->orderBy('published_at', 'desc');

        if ($q) {
            // simple title/description search; for production add full-text index
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            });
        }

        if ($source) $query->whereHas('source', fn($s) => $s->where('slug', $source));
        if ($category) $query->where('category', $category);
        if ($author) $query->where('author', 'like', "%{$author}%");
        if ($dateFrom) $query->where('published_at', '>=', $dateFrom);
        if ($dateTo) $query->where('published_at', '<=', $dateTo);

        $results = Cache::remember($cacheKey, 60, function () use ($query, $perPage) {
            return $query->paginate($perPage);
        });

        return ArticleResource::collection($results);
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
