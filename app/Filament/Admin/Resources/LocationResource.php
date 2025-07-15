<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LocationResource\Pages;
use App\Filament\Admin\Resources\LocationResource\RelationManagers;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LocationResource extends Resource
{
    protected static ?string $navigationGroup = 'Admin';

    protected static ?string $model = Location::class;

    protected static ?string $modelLabel = 'localidade';

    protected static ?string $pluralModelName = 'localidades';

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\TextInput::make('labren_id')
                    ->label('Id Labren')
                    ->required()
                    ->placeholder('Id Labren'),

                Forms\Components\TextInput::make('lat')
                    ->label('Latitude')
                    ->required()
                    ->placeholder('Latitude'),

                Forms\Components\TextInput::make('lon')
                    ->label('Longitude')
                    ->required()
                    ->placeholder('Longitude'),

                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->placeholder('Nome'),

                Forms\Components\TextInput::make('state')
                    ->label('Estado')
                    ->required()
                    ->placeholder('Estado'),

                Forms\Components\TextInput::make('annual_irradiation')
                    ->label('Irradiação Anual')
                    ->required()
                    ->placeholder('Irradiação Anual'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('labren_id')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->label('Id Labren'),

                Tables\Columns\TextColumn::make('lat')
                    ->label('Latitude'),

                Tables\Columns\TextColumn::make('lon')
                    ->label('Longitude'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Cidade'),

                Tables\Columns\TextColumn::make('state')
                    ->searchable()
                    ->label('Estado'),

                Tables\Columns\TextColumn::make('annual_irradiation')
                    ->sortable()
                    ->label('Irradiação anual'),
            ])
            ->searchPlaceholder('Buscar: id, cidade ou estado')
            ->filters([
                Tables\Filters\SelectFilter::make('state')
                    ->label('Estado')
                    ->multiple()
                    ->options(Location::pluck('state', 'state')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
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
            'index' => Pages\ManageLocations::route('/'),
        ];
    }
}
