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
        $sources = [
            [
                'slug'      => 'newsapi',
                'name'      => 'NewsAPI',
                'provider'  => \App\Services\Providers\NewsApiProvider::class,
                'api_env'   => 'NEWSAPI_KEY',
                'base_url'  => 'https://newsapi.org/v2/',
                'enabled'   => true,
            ],
            [
                'slug'      => 'guardian',
                'name'      => 'The Guardian',
                'provider'  => \App\Services\Providers\GuardianProvider::class,
                'api_env'   => 'GUARDIAN_KEY',
                'base_url'  => 'https://content.guardianapis.com/',
                'enabled'   => true,
            ],
            [
                'slug'      => 'nyt',
                'name'      => 'New York Times',
                'provider'  => \App\Services\Providers\NytProvider::class,
                'api_env'   => 'NYT_KEY',
                'base_url'  => 'https://api.nytimes.com/svc/topstories/v2/',
                'enabled'   => true,
            ],
        ];

        foreach ($sources as $s) {
            Source::updateOrCreate(
                ['slug' => $s['slug']],
                [
                    'name'   => $s['name'],
                    'config' => [
                        'provider_class' => $s['provider'],
                        'api_key'        => env($s['api_env']),
                        'base_url'       => $s['base_url'],
                    ],
                    'enabled' => $s['enabled'],
                ]
            );
        }
    }
}
