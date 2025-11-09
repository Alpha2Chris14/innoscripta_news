<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Source::updateOrCreate(
            ['slug' => 'newsapi'],
            [
                'name'   => 'NewsAPI',
                'config' => [
                    'provider_class' => \App\Services\Providers\NewsApiProvider::class,
                    'api_key'        => env('NEWSAPI_KEY'),
                    'base_url'       => 'https://newsapi.org/v2/',
                ],
                'enabled' => true,
            ]
        );

        Source::updateOrCreate(
            ['slug' => 'guardian'],
            [
                'name'   => 'The Guardian',
                'config' => [
                    'provider_class' => \App\Services\Providers\GuardianProvider::class,
                    'api_key'        => env('GUARDIAN_KEY'),
                    'base_url'       => 'https://content.guardianapis.com/',
                ],
                'enabled' => true,
            ]
        );

        Source::updateOrCreate(
            ['slug' => 'nyt'],
            [
                'name'   => 'New York Times',
                'config' => [
                    'provider_class' => \App\Services\Providers\NytProvider::class,
                    'api_key'        => env('NYT_KEY'),
                    'base_url'       => 'https://api.nytimes.com/svc/topstories/v2/',
                ],
                'enabled' => true,
            ]
        );
    }
}
