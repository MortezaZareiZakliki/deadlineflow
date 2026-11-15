<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Version;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        $project = Project::factory()->create();
        $version = Version::factory()->create(['project_id' => $project->id]);

        return [
            'user_id' => $project->user_id,
            'project_id' => $project->id,
            'version_id' => $version->id,
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['todo', 'in_progress', 'done']),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'deadline' => fake()->dateTimeBetween('now', '+30 days'),
        ];
    }
}