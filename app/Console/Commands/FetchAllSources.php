<?php

namespace App\Console\Commands;

use App\Jobs\FetchArticlesJob;
use App\Models\Source;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
        $this->info('Fetching articles started...');
        $sourceId = $this->option('source_id');
        $sources = $sourceId ? Source::where('id', $sourceId)->get() : Source::where('enabled', true)->get();

        info($sources->toArray());


        foreach ($sources as $source) {
            info("Hello from inside foreach loop");
            info("Dispatching job for source {$source->id}");
            FetchArticlesJob::dispatch($source->id)->onQueue('news-fetch');
            $this->info("Dispatched fetch job for source {$source->name}");
        }
        info('Fetching articles completed.');
    }
}
