<?php

namespace App\Filament\Admin\Resources\SizingResource\Pages;

use App\Filament\Admin\Resources\SizingResource;
use App\Models\Sizing;
use Filament\Actions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageSizings extends ManageRecords
{
    protected static string $resource = SizingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('consumo')
                ->label('Estimativa de Consumo')
                ->form([
                    Grid::make(2)->schema([
                        Select::make('residence_id')
                            ->label('Residência')
                            ->relationship(
                                'residence',
                                'label',
                                fn(Builder $query) =>
                                auth()->user()->hasRole('admin')
                                    ? $query
                                    : $query->where('user_id', auth()->id())
                            )
                            ->required()
                            ->native(false)
                            ->live(),
                        Placeholder::make('resultado')
                            ->label('Consumo estimado mensal')
                            ->content(function (Get $get) {
                                $residenceId = $get('residence_id');

                                if (!$residenceId) {
                                    return 'Selecione uma residência.';
                                }

                                $total = Sizing::where('residence_id', $residenceId)->sum('kwh');
                                return ceil($total) . ' kWh';
                            }),
                    ])
                ])
                ->modalHeading('Calcular Consumo Estimado')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Fechar'),
        ];
    }
}
