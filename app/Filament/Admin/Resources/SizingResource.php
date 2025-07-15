<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SizingResource\Pages;
use App\Filament\Admin\Resources\SizingResource\RelationManagers;
use App\Models\Residence;
use App\Models\Sizing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ManageSizings::route('/'),
        ];
    }
}
