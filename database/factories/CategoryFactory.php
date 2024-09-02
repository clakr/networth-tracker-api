<?php

namespace Database\Factories;

use App\Enums\CategoryType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
        ];
    }

    public function income(): static
    {
        return $this->state(fn () => ['type' => CategoryType::INCOME->value]);
    }

    public function expense(): static
    {
        return $this->state(fn () => ['type' => CategoryType::EXPENSE->value]);
    }
}
