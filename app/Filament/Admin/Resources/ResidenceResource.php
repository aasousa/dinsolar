<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ResidenceResource\Pages;
use App\Filament\Admin\Resources\ResidenceResource\RelationManagers;
use App\Models\Residence;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResidenceResource extends Resource
{
    protected static ?string $model = Residence::class;

    protected static ?string $modelLabel = 'residência';

    protected static ?string $pluralModelName = 'residências';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->label('Rótulo')
                    ->required()
                    ->placeholder('Casa, apartamento, etc.'),

                Forms\Components\Select::make('location_id')
                    ->label('Localidade')
                    ->required()
                    ->relationship('location', 'full_name')
                    ->getOptionLabelFromRecordUsing(fn (Location $record) => $record->full_name)
                    ->preload()
                    ->searchable(),

                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->label('Rótulo'),

                Tables\Columns\TextColumn::make('location.name')
                    ->label('Cidade')
                    ->searchable(),

                Tables\Columns\TextColumn::make('location.state')
                    ->label('Estado')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuário')
                    ->searchable()
                    ->hidden(!auth()->user()->hasRole('admin')),
            ])
            ->filters([
                //
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
            'index' => Pages\ManageResidences::route('/'),
        ];
    }
}
