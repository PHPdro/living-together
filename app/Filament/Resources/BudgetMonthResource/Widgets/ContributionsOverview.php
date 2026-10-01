<?php

namespace App\Filament\Resources\BudgetMonthResource\Widgets;

use App\Models\BudgetMonth;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class ContributionsOverview extends Widget
{
    protected static string $view = 'filament.resources.budget-month-resource.widgets.contributions-overview';

    protected static bool $isLazy = false;

    public ?BudgetMonth $record = null;

    protected int|string|array $columnSpan = 'full';

    /**
     * Get each partner's proportional contribution for the current record's month.
     *
     * @return Collection<int, array{user: User, income: float, income_share: float, due: float, paid: float, balance: float}>
     */
    public function getContributions(): Collection
    {
        return $this->record?->contributions() ?? collect();
    }
}
