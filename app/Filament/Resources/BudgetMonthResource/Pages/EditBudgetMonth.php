<?php

namespace App\Filament\Resources\BudgetMonthResource\Pages;

use App\Filament\Resources\BudgetMonthResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBudgetMonth extends EditRecord
{
    protected static string $resource = BudgetMonthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
