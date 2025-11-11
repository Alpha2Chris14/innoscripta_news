<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Source;
use App\Repositories\ArticleRepository;
use App\Services\Providers\BaseNewsProvider;

class FetchArticlesJob implements ShouldQueue
{
    use Queueable;

    public int $sourceId;

    /**
     * Create a new job instance for fetching articles.
     */
    public function __construct(int $sourceId)
    {
        $this->sourceId = $sourceId;
    }

    /**
     * Execute the job.
     */
    public function handle(ArticleRepository $repo): void
    {
        info("Inside FetchArticlesJob handle method");
        $source = Source::find($this->sourceId);

        info("After finding source");

        if (!$source || !$source->enabled) return;

        info("Source is enabled");

        $config = $source->config ?? [];
        $providerClass = $config['provider_class'] ?? null;

        info("Provider class resolved: {$providerClass}");

        if (!$providerClass || !class_exists($providerClass)) {
            Log::error("Provider class not found for source {$source->id}");
            return;
        }


        $provider = new $providerClass($config);

        info("Provider instance created for source {$source->id}");

        try {
            // Fetch latest articles
            $articles = $provider->fetchLatest(['pageSize' => 50]);
            $repo->upsertArticles($articles, $source->id);
        } catch (Exception $e) {
            // Log the error and rethrow to trigger job retry
            Log::error("FetchArticlesJob failed for source {$source->id}: {$e->getMessage()}");
            throw $e;
        }
    }


    // This is just for testing purposes to inject a mock provider

    public function handleWithProvider(BaseNewsProvider $provider): void
    {
        $sourceId = $this->sourceId;
        $articles = $provider->fetchLatest();

        foreach ($articles as $articleData) {
            \App\Models\Article::updateOrCreate(
                ['source_id' => $sourceId, 'title' => $articleData['title']],
                $articleData
            );
        }
    }
}
