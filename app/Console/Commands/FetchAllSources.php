<?php

namespace App\Console\Commands;

use App\Jobs\FetchArticlesJob;
use App\Models\Source;
use Illuminate\Console\Command;

class FetchAllSources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch {--source_id=}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch jobs to fetch articles for enabled sources';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sourceId = $this->option('source_id');
        $sources = $sourceId ? Source::where('id', $sourceId)->get() : Source::where('enabled', true)->get();

        foreach ($sources as $source) {
            FetchArticlesJob::dispatch($source->id)->onQueue('news-fetch');
            $this->info("Dispatched fetch job for source {$source->name}");
        }
    }
}
