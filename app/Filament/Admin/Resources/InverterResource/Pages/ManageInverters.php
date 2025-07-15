<?php

namespace App\Filament\Admin\Resources\InverterResource\Pages;

use App\Filament\Admin\Resources\InverterResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageInverters extends ManageRecords
{
    protected static string $resource = InverterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
