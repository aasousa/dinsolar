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
    protected static ?string $model = Location::class;

    protected static ?string $modelLabel = 'localidade';

    protected static ?string $pluralModelName = 'localidades';

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('labren_id')
                    ->label('Labren ID')
                    ->required()
                    ->placeholder('Labren ID'),

                Forms\Components\TextInput::make('lon')
                    ->label('Longitude')
                    ->required()
                    ->placeholder('Longitude'),

                Forms\Components\TextInput::make('lat')
                    ->label('Latitude')
                    ->required()
                    ->placeholder('Latitude'),

                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->placeholder('Name'),

                Forms\Components\TextInput::make('state')
                    ->label('State')
                    ->required()
                    ->placeholder('State'),

                Forms\Components\TextInput::make('annual_irradiation')
                    ->label('Annual Irradiation')
                    ->required()
                    ->placeholder('Annual Irradiation'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('labren_id')
                    ->searchable()
                    ->label('Labren ID'),

                Tables\Columns\TextColumn::make('lon')
                    ->searchable()
                    ->label('Longitude'),

                Tables\Columns\TextColumn::make('lat')
                    ->searchable()
                    ->label('Latitude'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Cidade'),

                Tables\Columns\TextColumn::make('state')
                    ->searchable()
                    ->label('Estado'),

                Tables\Columns\TextColumn::make('annual_irradiation')
                    ->searchable()
                    ->label('Irradiação anual'),

            ])
            ->filters([
                //
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
