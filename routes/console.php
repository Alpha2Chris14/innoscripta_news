<?php

use App\Console\Commands\FetchAllSources;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Artisan::command('news:fetch', function () {
//     $this->call(FetchAllSources::class);
// })->purpose('Fetch articles from all enabled news sources')->everyTenMinutes()->withoutOverlapping();

Artisan::command('news:fetch', function () {
    $this->call(FetchAllSources::class);
})->purpose('Fetch articles from all enabled news sources')->everyMinute()->withoutOverlapping();
