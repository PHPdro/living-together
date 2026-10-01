<?php

namespace App\Filament\Resources\BudgetMonthResource\RelationManagers;

use App\Enums\ExpenseType;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExpensesRelationManager extends RelationManager
{
    protected static string $relationship = 'expenses';

    protected static ?string $title = 'Despesas';

    protected static ?string $modelLabel = 'despesa';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')->label('Nome')->required(),
                    ]),
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('description')
                    ->label('Descrição')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('category.name')
                    ->label('Categoria')
                    ->badge()
                    ->color(fn ($record) => $record->category?->color)
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
                TextColumn::make('due_date')
                    ->label('Vencimento')
                    ->date('d/m/Y')
                    ->placeholder('—'),
            ])
            ->filters([
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
            ->defaultSort('description')
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
