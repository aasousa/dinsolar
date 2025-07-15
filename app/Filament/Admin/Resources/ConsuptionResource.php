<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ConsuptionResource\Pages;
use App\Filament\Admin\Resources\ConsuptionResource\RelationManagers;
use App\Models\Consuption;
use App\Models\Residence;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConsuptionResource extends Resource
{
    protected static ?string $model = Consuption::class;

    protected static ?string $modelLabel = 'consumo';

    protected static ?string $pluralModelName = 'consumos';

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

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

                Forms\Components\DatePicker::make('date')
                    ->label('Data')
                    ->required()
                    ->placeholder('Data'),

                Forms\Components\TextInput::make('kwh')
                    ->label('Consumo (kWh)')
                    ->required()
                    ->placeholder('kWh'),

                Forms\Components\TextInput::make('te')
                    ->label('Tarifa de Energia (TE)')
                    ->prefixIcon('heroicon-o-currency-dollar')
                    ->numeric()
                    ->mask('9.99999')
                    ->placeholder('0.00000'),

                Forms\Components\TextInput::make('tusd')
                    ->label('Tarifa de Uso do Sistema (TUSD)')
                    ->prefixIcon('heroicon-o-currency-dollar')
                    ->numeric()
                    ->mask('9.99999')
                    ->placeholder('0.00000'),

                Forms\Components\TextInput::make('ammount')
                    ->label('Valor')
                    ->prefixIcon('heroicon-o-currency-dollar')
                    ->numeric()
                    ->placeholder('Total pago (incluindo impostos)'),

                Forms\Components\Select::make('flag')
                    ->label('Bandeira')
                    ->options([
                        'green' => 'Verde',
                        'yellow' => 'Amarela',
                        'red_1' => 'Vermelha - Patamar 1',
                        'red_2' => 'Vermelha - Patamar 2',
                    ])
                    ->prefixIcon('heroicon-s-flag')
                    ->prefixIconColor(fn($state) => match ($state) {
                        'green' => 'success',
                        'yellow' => 'warning',
                        'red_1' => 'danger',
                        'red_2' => 'danger',
                        default => 'gray'
                    })
                    ->live(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('residence.label')
                    ->label('Residência')
                    ->searchable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('Mês/Ano')
                    ->formatStateUsing(fn($state) => date('m/Y', strtotime($state)))
                    ->searchable(),

                Tables\Columns\TextColumn::make('kwh')
                    ->label('Consumo')
                    ->suffix(' kWh'),

                Tables\Columns\TextColumn::make('te')
                    ->label('TE'),

                Tables\Columns\TextColumn::make('tusd')
                    ->label('TUSD'),

                Tables\Columns\IconColumn::make('flag')
                    ->label('Bandeira')
                    ->icon('heroicon-s-flag')
                    ->color(fn(string $state): string => match ($state) {
                        'green' => 'success',
                        'yellow' => 'warning',
                        'red_1' => 'danger',
                        'red_2' => 'danger',
                        default => 'gray'
                    }),

                Tables\Columns\TextColumn::make('ammount')
                    ->label('Total pago')
                    ->money('BRL'),
            ])
            ->filters([
                SelectFilter::make('user')
                    ->label('Usuário')
                    ->relationship('residence.user', 'name')
                    ->preload()
                    ->visible(fn() => auth()->user()->hasRole('admin'))
                    ->searchable(),

                SelectFilter::make('residence')
                    ->label('Residência')
                    ->relationship(
                        'residence',
                        'label',
                        fn(Builder $query) =>
                        auth()->user()->hasRole('admin')
                            ? $query
                            : $query->where('user_id', auth()->id())
                    )
                    ->preload()
                    ->searchable(),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            // ->withoutGlobalScopes([
            //     SoftDeletingScope::class,
            // ]);
            // Mostra apenas os consumos das residências do usuário
            // autenticado a menos que o usuário seja um administrador
            ->when(
                !auth()->user()->hasRole('admin'),
                fn(Builder $query) => $query
                    ->whereHas('residence', fn(Builder $q) => $q->where('user_id', auth()->id()))
            );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageConsuptions::route('/'),
        ];
    }
}
