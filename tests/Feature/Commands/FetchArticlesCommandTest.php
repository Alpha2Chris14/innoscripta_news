<?php

namespace Tests\Feature\Commands;

use App\Console\Commands\FetchArticlesCommand;
use App\Jobs\FetchArticlesJob;
use App\Models\Source;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class FetchArticlesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_dispatches_jobs_for_enabled_sources()
    {
        Bus::fake();

        Source::factory()->create(['enabled' => true]);
        Source::factory()->create(['enabled' => true]);

        $this->artisan('news:fetch')
            ->expectsOutput('Fetching articles started...')
            ->assertExitCode(0);

        Bus::assertDispatched(FetchArticlesJob::class, 2);
    }
}
