<?php

namespace App\Filament\Resources\BudgetMonthResource\Pages;

use App\Filament\Resources\BudgetMonthResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBudgetMonths extends ListRecords
{
    protected static string $resource = BudgetMonthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
