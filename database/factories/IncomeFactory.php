<?php

namespace Database\Factories;

use App\Models\BudgetMonth;
use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Income>
 */
class IncomeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'budget_month_id' => BudgetMonth::factory(),
            'user_id' => User::factory(),
            'description' => 'Salário',
            'amount' => $this->faker->randomFloat(2, 2000, 9000),
        ];
    }
}
