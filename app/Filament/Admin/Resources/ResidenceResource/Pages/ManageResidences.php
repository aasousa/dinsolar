<?php

namespace App\Filament\Admin\Resources\ResidenceResource\Pages;

use App\Filament\Admin\Resources\ResidenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageResidences extends ManageRecords
{
    protected static string $resource = ResidenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
