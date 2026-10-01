<?php

namespace Tests\Feature;

use App\Models\BudgetMonth;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FilamentPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_authenticated_user_can_view_the_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertOk();
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function resourceIndexPages(): array
    {
        return [
            'months' => ['/admin/budget-months'],
            'expenses' => ['/admin/expenses'],
            'incomes' => ['/admin/incomes'],
            'categories' => ['/admin/categories'],
        ];
    }

    #[DataProvider('resourceIndexPages')]
    public function test_authenticated_user_can_view_resource_index(string $path): void
    {
        $this->actingAs(User::factory()->create())
            ->get($path)
            ->assertOk();
    }

    #[DataProvider('resourceIndexPages')]
    public function test_authenticated_user_can_view_resource_create_page(string $path): void
    {
        $this->actingAs(User::factory()->create())
            ->get("{$path}/create")
            ->assertOk();
    }

    public function test_authenticated_user_can_view_a_budget_month_with_its_contribution_breakdown(): void
    {
        $budgetMonth = BudgetMonth::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get("/admin/budget-months/{$budgetMonth->id}")
            ->assertOk()
            ->assertSee($budgetMonth->label());
    }
}
