<?php

namespace App\Filament\Widgets;

use App\Models\BudgetMonth;
use Filament\Widgets\ChartWidget;

class ExpensesByCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Despesas por categoria (mês atual)';

    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '320px';

    protected int|string|array $columnSpan = [
        'md' => 1,
    ];

    /**
     * Fixed categorical color order, applied by rank rather than cycled hue generation.
     *
     * @var array<int, string>
     */
    protected const PALETTE = [
        '#2a78d6', '#eb6834', '#1baf7a', '#eda100',
        '#e87ba4', '#008300', '#4a3aa7', '#e34948',
    ];

    protected function getData(): array
    {
        $month = BudgetMonth::current();

        $totals = $month
            ? $month->expenses()
                ->selectRaw('category_id, sum(amount) as total')
                ->groupBy('category_id')
                ->with('category:id,name')
                ->orderByDesc('total')
                ->get()
                ->mapWithKeys(fn ($row) => [($row->category?->name ?? 'Sem categoria') => (float) $row->total])
            : collect();

        // Cap at 7 named slots and fold the remainder into "Outras" to stay within the categorical palette.
        $top = $totals->take(7);
        $rest = $totals->skip(7)->sum();

        if ($rest > 0) {
            $top->put('Outras', $rest);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total gasto',
                    'data' => $top->values(),
                    'backgroundColor' => array_slice(self::PALETTE, 0, $top->count()),
                ],
            ],
            'labels' => $top->keys(),
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
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
