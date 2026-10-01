<?php

namespace Database\Seeders;

use App\Enums\ExpenseType;
use App\Models\BudgetMonth;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BudgetDemoSeeder extends Seeder
{
    /**
     * Seed two partners, a set of categories, and six months of demo
     * expenses/income so the dashboard charts have something to show.
     */
    public function run(): void
    {
        $you = User::firstOrCreate(
            ['email' => 'dev@wplugin.com.br'],
            ['name' => 'Pedro', 'password' => 'password', 'email_verified_at' => now()],
        );

        $partner = User::firstOrCreate(
            ['email' => 'partner@example.com'],
            ['name' => 'Gabriela', 'password' => 'password', 'email_verified_at' => now()],
        );

        $categories = collect([
            ['name' => 'Moradia', 'color' => '#2a78d6'],
            ['name' => 'Contas', 'color' => '#eb6834'],
            ['name' => 'Alimentação', 'color' => '#1baf7a'],
            ['name' => 'Transporte', 'color' => '#eda100'],
            ['name' => 'Saúde', 'color' => '#e87ba4'],
            ['name' => 'Lazer', 'color' => '#4a3aa7'],
        ])->map(fn (array $data) => Category::firstOrCreate(['name' => $data['name']], $data));

        [$moradia, $contas, $alimentacao, $transporte, $saude, $lazer] = $categories->all();

        $fixedExpenses = [
            ['description' => 'Aluguel', 'category' => $moradia, 'amount' => 1800],
            ['description' => 'Condomínio', 'category' => $moradia, 'amount' => 450],
            ['description' => 'Internet', 'category' => $contas, 'amount' => 120],
            ['description' => 'Energia elétrica', 'category' => $contas, 'amount' => 210],
        ];

        $variablePool = [
            ['description' => 'Supermercado', 'category' => $alimentacao, 'range' => [400, 900]],
            ['description' => 'Restaurantes', 'category' => $alimentacao, 'range' => [100, 350]],
            ['description' => 'Combustível', 'category' => $transporte, 'range' => [150, 350]],
            ['description' => 'Aplicativo de transporte', 'category' => $transporte, 'range' => [40, 150]],
            ['description' => 'Farmácia', 'category' => $saude, 'range' => [50, 250]],
            ['description' => 'Cinema e streaming', 'category' => $lazer, 'range' => [60, 220]],
            ['description' => 'Presentes', 'category' => $lazer, 'range' => [0, 200]],
        ];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);

            $budgetMonth = BudgetMonth::firstOrCreate([
                'year' => $date->year,
                'month' => $date->month,
            ]);

            if (! $budgetMonth->wasRecentlyCreated) {
                continue;
            }

            Income::create([
                'budget_month_id' => $budgetMonth->id,
                'user_id' => $you->id,
                'description' => 'Salário',
                'amount' => fake()->randomFloat(2, 4200, 5800),
            ]);

            Income::create([
                'budget_month_id' => $budgetMonth->id,
                'user_id' => $partner->id,
                'description' => 'Salário',
                'amount' => fake()->randomFloat(2, 2800, 4600),
            ]);

            foreach ($fixedExpenses as $fixed) {
                Expense::create([
                    'budget_month_id' => $budgetMonth->id,
                    'category_id' => $fixed['category']->id,
                    'description' => $fixed['description'],
                    'amount' => $fixed['amount'],
                    'type' => ExpenseType::Fixed,
                    'paid_by_user_id' => $you->id,
                ]);
            }

            foreach (fake()->randomElements($variablePool, random_int(4, 6)) as $variable) {
                Expense::create([
                    'budget_month_id' => $budgetMonth->id,
                    'category_id' => $variable['category']->id,
                    'description' => $variable['description'],
                    'amount' => fake()->randomFloat(2, $variable['range'][0], $variable['range'][1]),
                    'type' => ExpenseType::Variable,
                    'paid_by_user_id' => fake()->randomElement([$you->id, $partner->id]),
                ]);
            }
        }
    }
}
