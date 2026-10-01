<?php

namespace App\Filament\Widgets;

use App\Models\BudgetMonth;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class MonthlyStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $month = BudgetMonth::current();

        if (! $month) {
            return [
                Stat::make('Nenhum mês cadastrado', 'Comece em "Meses"')
                    ->description('Cadastre o mês atual para ver os totais aqui')
                    ->color('gray')
                    ->icon('heroicon-o-calendar-date-range'),
            ];
        }

        $totalIncome = $month->totalIncome();
        $totalExpenses = $month->totalExpenses();
        $balance = $totalIncome - $totalExpenses;

        $stats = [
            Stat::make('Ganhos — '.$month->label(), Number::currency($totalIncome, 'BRL'))
                ->color('success')
                ->icon('heroicon-o-arrow-trending-up'),
            Stat::make('Despesas — '.$month->label(), Number::currency($totalExpenses, 'BRL'))
                ->color('danger')
                ->icon('heroicon-o-arrow-trending-down'),
            Stat::make('Saldo do mês', Number::currency($balance, 'BRL'))
                ->color($balance >= 0 ? 'success' : 'danger')
                ->icon($balance >= 0 ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle'),
        ];

        foreach ($month->contributions() as $contribution) {
            if ($contribution['income'] <= 0 && $contribution['paid'] <= 0) {
                continue;
            }

            $stats[] = Stat::make(
                $contribution['user']->name.' deve contribuir',
                Number::currency($contribution['due'], 'BRL'),
            )
                ->description(Number::percentage($contribution['income_share'] * 100, maxPrecision: 1).' da renda do casal')
                ->color('info')
                ->icon('heroicon-o-user');
        }

        return $stats;
    }
}
