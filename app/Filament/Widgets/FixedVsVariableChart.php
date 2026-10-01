<?php

namespace App\Filament\Widgets;

use App\Enums\ExpenseType;
use App\Models\BudgetMonth;
use Filament\Widgets\ChartWidget;

class FixedVsVariableChart extends ChartWidget
{
    protected static ?string $heading = 'Fixas vs. variáveis (mês atual)';

    protected static ?int $sort = 4;

    protected static ?string $maxHeight = '200px';

    protected int|string|array $columnSpan = [
        'md' => 1,
    ];

    protected function getData(): array
    {
        $month = BudgetMonth::current();

        $fixed = $month?->expenses()->where('type', ExpenseType::Fixed)->sum('amount') ?? 0;
        $variable = $month?->expenses()->where('type', ExpenseType::Variable)->sum('amount') ?? 0;

        return [
            'datasets' => [
                [
                    'label' => ExpenseType::Fixed->label(),
                    'data' => [(float) $fixed],
                    'backgroundColor' => '#2a78d6',
                ],
                [
                    'label' => ExpenseType::Variable->label(),
                    'data' => [(float) $variable],
                    'backgroundColor' => '#eb6834',
                ],
            ],
            'labels' => ['Despesas do mês'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'x' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                ],
                'y' => [
                    'stacked' => true,
                ],
            ],
        ];
    }
}
