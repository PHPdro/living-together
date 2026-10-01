<?php

namespace App\Filament\Widgets;

use App\Models\BudgetMonth;
use Filament\Widgets\ChartWidget;

class ExpensesTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Despesas por mês';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '280px';

    protected int|string|array $columnSpan = [
        'md' => 2,
    ];

    protected function getData(): array
    {
        $months = BudgetMonth::query()
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->sortBy(fn (BudgetMonth $month) => sprintf('%04d-%02d', $month->year, $month->month))
            ->take(-12);

        return [
            'datasets' => [
                [
                    'label' => 'Despesas totais',
                    'data' => $months->map(fn (BudgetMonth $month) => $month->totalExpenses())->values(),
                    'borderColor' => '#2a78d6',
                    'backgroundColor' => 'rgba(42, 120, 214, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $months->map(fn (BudgetMonth $month) => $month->label())->values(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
