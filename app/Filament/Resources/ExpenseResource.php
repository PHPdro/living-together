<?php

namespace App\Filament\Resources;

use App\Enums\ExpenseType;
use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\BudgetMonth;
use App\Models\Expense;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
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

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';

    protected static ?string $navigationGroup = 'Financeiro';

    protected static ?string $navigationLabel = 'Despesas';

    protected static ?string $modelLabel = 'despesa';

    protected static ?string $pluralModelLabel = 'despesas';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('budget_month_id')
                    ->label('Mês')
                    ->options(fn () => BudgetMonth::query()->orderByDesc('year')->orderByDesc('month')->get()->mapWithKeys(fn (BudgetMonth $month) => [$month->id => $month->label()]))
                    ->required()
                    ->searchable(),
                TextInput::make('description')
                    ->label('Descrição')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Radio::make('type')
                    ->label('Tipo')
                    ->options([
                        ExpenseType::Fixed->value => ExpenseType::Fixed->label(),
                        ExpenseType::Variable->value => ExpenseType::Variable->label(),
                    ])
                    ->default(ExpenseType::Variable->value)
                    ->required()
                    ->inline(),
                TextInput::make('amount')
                    ->label('Valor')
                    ->required()
                    ->numeric()
                    ->prefix('R$')
                    ->minValue(0),
                Select::make('category_id')
                    ->label('Categoria')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('paid_by_user_id')
                    ->label('Pago por')
                    ->options(fn () => User::query()->pluck('name', 'id'))
                    ->searchable(),
                DatePicker::make('due_date')
                    ->label('Vencimento')
                    ->native(false),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('budgetMonth')
                    ->label('Mês')
                    ->state(fn (Expense $record) => $record->budgetMonth->label())
                    ->sortable(['year', 'month']),
                TextColumn::make('description')
                    ->label('Descrição')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('category.name')
                    ->label('Categoria')
                    ->badge()
                    ->color(fn (Expense $record) => $record->category?->color)
                    ->placeholder('Sem categoria'),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (ExpenseType $state) => $state->label())
                    ->color(fn (ExpenseType $state) => $state->color()),
                TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),
                TextColumn::make('paidBy.name')
                    ->label('Pago por')
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('budget_month_id')
                    ->label('Mês')
                    ->options(fn () => BudgetMonth::query()->orderByDesc('year')->orderByDesc('month')->get()->mapWithKeys(fn (BudgetMonth $month) => [$month->id => $month->label()])),
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        ExpenseType::Fixed->value => ExpenseType::Fixed->label(),
                        ExpenseType::Variable->value => ExpenseType::Variable->label(),
                    ]),
                SelectFilter::make('category_id')
                    ->label('Categoria')
                    ->relationship('category', 'name'),
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
