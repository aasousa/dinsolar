<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $navigationGroup = 'Admin';

    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'usuário';

    protected static ?string $pluralModelName = 'usuários';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->placeholder('Nome'),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->placeholder('Email'),

                Forms\Components\TextInput::make('password')
                    ->label('Senha')
                    ->required()
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->visible(fn($record) => !filled($record)),

                Forms\Components\TextInput::make('password')
                    ->label('Senha')
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    // ->dehydrated(fn ($state) => filled($state))
                    // ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->visible(fn($record) => filled($record))
                    ->helperText('Deixe em branco para não alterar a senha'),

                Forms\Components\Select::make('roles')
                    ->label('Funções')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->disabled(!auth()->user()->hasRole('admin'))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email'),

                Tables\Columns\TextColumn::make('residences.label')
                    ->label('Residências')
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em'),
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
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
