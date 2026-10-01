<?php

namespace Tests\Unit;

use App\Enums\ExpenseType;
use App\Models\BudgetMonth;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetMonthContributionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_user_contributes_proportionally_to_their_income(): void
    {
        $budgetMonth = BudgetMonth::factory()->create();

        $userA = User::factory()->create();
        $userB = User::factory()->create();

        // Income share: A earns 6000 (60%), B earns 4000 (40%) of the 10000 total.
        Income::factory()->for($budgetMonth)->for($userA)->create(['amount' => 6000]);
        Income::factory()->for($budgetMonth)->for($userB)->create(['amount' => 4000]);

        // Total expenses: 1000, all paid by A.
        Expense::factory()->for($budgetMonth)->create([
            'amount' => 1000,
            'type' => ExpenseType::Fixed,
            'paid_by_user_id' => $userA->id,
        ]);

        $contributions = $budgetMonth->contributions()->keyBy(fn (array $c) => $c['user']->id);

        $this->assertEqualsWithDelta(0.6, $contributions[$userA->id]['income_share'], 0.0001);
        $this->assertEqualsWithDelta(0.4, $contributions[$userB->id]['income_share'], 0.0001);

        $this->assertEqualsWithDelta(600.0, $contributions[$userA->id]['due'], 0.01);
        $this->assertEqualsWithDelta(400.0, $contributions[$userB->id]['due'], 0.01);

        // A paid the full 1000 but only owed 600, so A is owed 400 back; B owes 400.
        $this->assertEqualsWithDelta(400.0, $contributions[$userA->id]['balance'], 0.01);
        $this->assertEqualsWithDelta(-400.0, $contributions[$userB->id]['balance'], 0.01);
    }

    public function test_income_share_is_zero_when_no_income_is_recorded(): void
    {
        $budgetMonth = BudgetMonth::factory()->create();
        User::factory()->create();

        Expense::factory()->for($budgetMonth)->create(['amount' => 500]);

        $contribution = $budgetMonth->contributions()->first();

        $this->assertSame(0.0, $contribution['income_share']);
        $this->assertSame(0.0, $contribution['due']);
    }

    public function test_totals_reflect_related_expenses_and_incomes(): void
    {
        $budgetMonth = BudgetMonth::factory()->create();

        Expense::factory()->for($budgetMonth)->count(3)->sequence(
            ['amount' => 100],
            ['amount' => 200],
            ['amount' => 300],
        )->create();

        Income::factory()->for($budgetMonth)->create(['amount' => 1000]);

        $this->assertSame(600.0, $budgetMonth->totalExpenses());
        $this->assertSame(1000.0, $budgetMonth->totalIncome());
    }
}
