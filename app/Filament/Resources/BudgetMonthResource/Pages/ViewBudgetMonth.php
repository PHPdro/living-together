<?php

namespace App\Filament\Resources\BudgetMonthResource\Pages;

use App\Filament\Resources\BudgetMonthResource;
use App\Filament\Resources\BudgetMonthResource\Widgets\ContributionsOverview;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBudgetMonth extends ViewRecord
{
    protected static string $resource = BudgetMonthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ContributionsOverview::class,
        ];
    }
}
