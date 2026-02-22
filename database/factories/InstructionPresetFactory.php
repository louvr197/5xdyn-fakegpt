<?php

namespace Database\Factories;

use App\Models\InstructionPreset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstructionPresetFactory extends Factory
{
    protected $model = InstructionPreset::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'icon' => '📝',
            'about' => $this->faker->paragraph(),
            'behavior' => $this->faker->paragraph(),
            'commands' => null,
            'preferred_model' => 'openai/gpt-4',
            'is_system' => true,
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
            'is_system' => false,
        ]);
    }

    public function system(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'is_system' => true,
        ]);
    }
}
