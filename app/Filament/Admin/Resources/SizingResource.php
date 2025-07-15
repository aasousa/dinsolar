<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SizingResource\Pages;
use App\Filament\Admin\Resources\SizingResource\RelationManagers;
use App\Models\Residence;
use App\Models\Sizing;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SizingResource extends Resource
{
    protected static ?string $model = Sizing::class;

    protected static ?string $modelLabel = 'dimensionamento';

    protected static ?string $pluralModelName = 'dimensionamentos';

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\Select::make('residence_id')
                    ->label('Residência')
                    ->required()
                    ->relationship(
                        'residence',
                        'label',
                        fn(Builder $query) =>
                        auth()->user()->hasRole('admin')
                            ? $query
                            : $query->where('user_id', auth()->id())
                    )
                    ->getOptionLabelFromRecordUsing(fn(Residence $record) => $record->label)
                    ->preload()
                    ->searchable(),
                Forms\Components\TextInput::make('name')
                    ->label('Nome do Aparelho')
                    ->required(),

                Forms\Components\TextInput::make('days')
                    ->label('Dias de uso por mês')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->maxValue(31)
                    ->live(),

                Forms\Components\TextInput::make('hours')
                    ->label('Horas de uso por dia')
                    ->numeric()
                    ->required()
                    ->minValue(0.1)
                    ->maxValue(24)
                    ->live(),

                Forms\Components\TextInput::make('kw')
                    ->label('Consumo (kW - Selo Procel)')
                    ->numeric()
                    ->required()
                    ->minValue(0.001)
                    ->step(0.001)
                    ->live(),

                Forms\Components\Placeholder::make('kwh')
                    ->label('Consumo mensal estimado (kWh)')
                    ->content(function (Get $get) {
                        $days = $get('days');
                        $hours = $get('hours');
                        $power = $get('kw');

                        if (!$days || !$hours || !$power) return 'Preencha todos os campos obrigatórios.';

                        return number_format($days * $hours * $power, 2) . ' kWh';
                    }),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas(
                'residence',
                fn($query) =>
                auth()->user()->hasRole('admin')
                    ? $query
                    : $query->where('user_id', auth()->id())
            );
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('residence.label')
                    ->label('Residência')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Aparelho')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('days')->label('Dias'),
                TextColumn::make('hours')->label('Horas/dia'),
                TextColumn::make('kw')->label('Potência (kW)'),
                TextColumn::make('kwh')
                    ->label('Consumo (kWh/mês)')
                    ->sortable()
                    ->numeric()
                    ->formatStateUsing(fn($state) => number_format($state, 2) . ' kWh'),
            ])
            ->filters([
                SelectFilter::make('residence_id')
                    ->label('Residência')
                    ->relationship(
                        'residence',
                        'label',
                        fn(Builder $query) =>
                        auth()->user()->hasRole('admin')
                            ? $query
                            : $query->where('user_id', auth()->id())
                    )
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSizings::route('/'),
        ];
    }
}
