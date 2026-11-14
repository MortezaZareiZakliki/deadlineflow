<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class VersionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'user_id' => function (array $attributes) {
                return Project::find($attributes['project_id'])->user_id;
            },
            'version_number' => fake()->numerify('#.#.#'),
            'description' => fake()->sentence(),
            'release_date' => now()->addDays(30),
        ];
    }
}