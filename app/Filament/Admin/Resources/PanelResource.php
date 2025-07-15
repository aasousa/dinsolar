<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PanelResource\Pages;
use App\Filament\Admin\Resources\PanelResource\RelationManagers;
use App\Models\Panel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class PanelResource extends Resource
{
    protected static ?string $navigationGroup = 'Admin';

    protected static ?string $model = Panel::class;

    protected static ?string $modelLabel = 'painel';

    protected static ?string $pluralLabel = 'paineis';

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationLabel = 'Paineis';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->placeholder('Nome'),

                Forms\Components\TextInput::make('description')
                    ->label('Descrição')
                    ->placeholder('Descrição'),

                Forms\Components\TextInput::make('brand')
                    ->label('Marca')
                    ->placeholder('Marca'),

                Forms\Components\TextInput::make('power')
                    ->label('Potência')
                    ->placeholder('Potência')
                    ->numeric()
                    ->required()
                    ->suffix('Wp'),

                Forms\Components\TextInput::make('weight')
                    ->label('Peso')
                    ->placeholder('9999')
                    ->numeric()
                    ->suffix('g'),

                Forms\Components\TextInput::make('dimensions')
                    ->label('Dimensões')
                    ->placeholder('9999x999x99')
                    ->suffix('mm'),

                Forms\Components\TextInput::make('price')
                    ->label('Preço')
                    ->placeholder('9999.99')
                    ->numeric()
                    ->required()
                    ->prefix('R$'),

                Forms\Components\FileUpload::make('datasheet')
                    ->label('Datasheet')
                    ->directory('datasheets')
                    ->visibility('public')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(2048),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Descrição')
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                Tables\Columns\TextColumn::make('brand')
                    ->label('Marca')
                    ->searchable(),

                Tables\Columns\TextColumn::make('power')
                    ->label('Potência')
                    ->searchable()
                    ->formatStateUsing(fn($state) => $state . ' Wp'),

                Tables\Columns\TextColumn::make('weight')
                    ->label('Peso')
                    ->formatStateUsing(fn($state) => number_format($state / 1000, 2) . ' kg'),

                Tables\Columns\TextColumn::make('dimensions')
                    ->label('Dimensões')
                    ->formatStateUsing(fn($state) => $state . ' mm'),
            ])
            ->filters([
                //
            ])
            ->searchPlaceholder("Buscar por nome, marca ou potência")
            ->actions([
                // action to open file
                Tables\Actions\Action::make('open')
                    ->label('Datasheet')
                    ->url(fn($record) => Storage::url($record->datasheet))
                    ->visible(fn($record) => $record->datasheet)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-document'),
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
            'index' => Pages\ManagePanels::route('/'),
        ];
    }
}
