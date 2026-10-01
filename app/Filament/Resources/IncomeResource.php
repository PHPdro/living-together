<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncomeResource\Pages;
use App\Models\BudgetMonth;
use App\Models\Income;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class IncomeResource extends Resource
{
    protected static ?string $model = Income::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?string $navigationGroup = 'Financeiro';

    protected static ?string $navigationLabel = 'Ganhos';

    protected static ?string $modelLabel = 'ganho';

    protected static ?string $pluralModelLabel = 'ganhos';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('budget_month_id')
                    ->label('Mês')
                    ->options(fn () => BudgetMonth::query()->orderByDesc('year')->orderByDesc('month')->get()->mapWithKeys(fn (BudgetMonth $month) => [$month->id => $month->label()]))
                    ->required()
                    ->searchable(),
                Select::make('user_id')
                    ->label('Pessoa')
                    ->options(fn () => User::query()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                TextInput::make('amount')
                    ->label('Valor')
                    ->required()
                    ->numeric()
                    ->prefix('R$')
                    ->minValue(0),
                TextInput::make('description')
                    ->label('Descrição')
                    ->maxLength(255)
                    ->placeholder('Ex.: Salário, freelance...')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('budgetMonth')
                    ->label('Mês')
                    ->state(fn (Income $record) => $record->budgetMonth->label())
                    ->sortable(['year', 'month']),
                TextColumn::make('user.name')
                    ->label('Pessoa')
                    ->weight('bold'),
                TextColumn::make('description')
                    ->label('Descrição')
                    ->placeholder('—'),
                TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('budget_month_id')
                    ->label('Mês')
                    ->options(fn () => BudgetMonth::query()->orderByDesc('year')->orderByDesc('month')->get()->mapWithKeys(fn (BudgetMonth $month) => [$month->id => $month->label()])),
                SelectFilter::make('user_id')
                    ->label('Pessoa')
                    ->options(fn () => User::query()->pluck('name', 'id')),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncomes::route('/'),
            'create' => Pages\CreateIncome::route('/create'),
            'edit' => Pages\EditIncome::route('/{record}/edit'),
        ];
    }
}
