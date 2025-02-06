<?php

namespace App\Filament\Admin\Resources\ConsuptionResource\Pages;

use App\Filament\Admin\Resources\ConsuptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageConsuptions extends ManageRecords
{
    protected static string $resource = ConsuptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
