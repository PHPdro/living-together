<?php

namespace Database\Factories;

use App\Enums\ExpenseType;
use App\Models\BudgetMonth;
use App\Models\Category;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
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
            'category_id' => Category::factory(),
            'paid_by_user_id' => null,
            'description' => ucfirst($this->faker->words(3, true)),
            'amount' => $this->faker->randomFloat(2, 20, 800),
            'type' => $this->faker->randomElement(ExpenseType::cases()),
            'due_date' => null,
        ];
    }

    /**
     * Indicate that the expense is a fixed, recurring monthly expense.
     */
    public function fixed(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ExpenseType::Fixed,
        ]);
    }

    /**
     * Indicate that the expense is a variable, one-off expense.
     */
    public function variable(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ExpenseType::Variable,
        ]);
    }
}
