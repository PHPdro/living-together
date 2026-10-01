<?php

namespace Database\Factories;

use App\Models\BudgetMonth;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BudgetMonth>
 */
class BudgetMonthFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = $this->faker->unique()->dateTimeBetween('-11 months', 'now');

        return [
            'year' => (int) $date->format('Y'),
            'month' => (int) $date->format('n'),
            'notes' => null,
        ];
    }
}
