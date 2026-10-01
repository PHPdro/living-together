<?php

namespace App\Filament\Resources\BudgetMonthResource\RelationManagers;

use App\Models\User;
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
use Filament\Tables\Table;

class IncomesRelationManager extends RelationManager
{
    protected static string $relationship = 'incomes';

    protected static ?string $title = 'Ganhos';

    protected static ?string $modelLabel = 'ganho';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
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
            ->defaultSort('amount', 'desc')
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
