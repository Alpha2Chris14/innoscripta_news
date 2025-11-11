<?php

namespace Database\Factories;

use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;

class SourceFactory extends Factory
{
    protected $model = Source::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'slug' => $this->faker->slug,
            'config' => [
                'provider_class' => \App\Services\Providers\NewsApiProvider::class,
                'api_key' => 'fake-key',
                'base_url' => 'https://example.com/',
            ],
            'enabled' => true,
        ];
    }
}
