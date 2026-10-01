<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BudgetMonthResource\Pages;
use App\Filament\Resources\BudgetMonthResource\RelationManagers\ExpensesRelationManager;
use App\Filament\Resources\BudgetMonthResource\RelationManagers\IncomesRelationManager;
use App\Models\BudgetMonth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BudgetMonthResource extends Resource
{
    protected static ?string $model = BudgetMonth::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';

    protected static ?string $navigationGroup = 'Financeiro';

    protected static ?string $navigationLabel = 'Meses';

    protected static ?string $modelLabel = 'mês';

    protected static ?string $pluralModelLabel = 'meses';

    protected static ?int $navigationSort = 1;

    /**
     * Get the options for the month select field (1 to 12).
     *
     * @return array<int, string>
     */
    public static function monthOptions(): array
    {
        return [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('month')
                    ->label('Mês')
                    ->options(self::monthOptions())
                    ->required(),
                TextInput::make('year')
                    ->label('Ano')
                    ->required()
                    ->numeric()
                    ->default(now()->year)
                    ->minValue(2000)
                    ->maxValue(2100),
                Textarea::make('notes')
                    ->label('Observações')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label('Mês')
                    ->state(fn (BudgetMonth $record) => $record->label())
                    ->weight('bold')
                    ->sortable(['year', 'month']),
                TextColumn::make('total_income')
                    ->label('Ganhos totais')
                    ->state(fn (BudgetMonth $record) => $record->totalIncome())
                    ->money('BRL'),
                TextColumn::make('total_expenses')
                    ->label('Despesas totais')
                    ->state(fn (BudgetMonth $record) => $record->totalExpenses())
                    ->money('BRL'),
                TextColumn::make('expenses_count')
                    ->label('Nº despesas')
                    ->counts('expenses')
                    ->alignCenter(),
            ])
            ->defaultSort('year', 'desc')
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ExpensesRelationManager::class,
            IncomesRelationManager::class,
        ];
    }

    /**
     * Eager-load relationship sums used by the table so listing months stays fast.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('expenses');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBudgetMonths::route('/'),
            'create' => Pages\CreateBudgetMonth::route('/create'),
            'view' => Pages\ViewBudgetMonth::route('/{record}'),
            'edit' => Pages\EditBudgetMonth::route('/{record}/edit'),
        ];
    }
}
