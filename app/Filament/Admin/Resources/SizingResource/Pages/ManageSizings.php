<?php

namespace App\Filament\Admin\Resources\SizingResource\Pages;

use App\Filament\Admin\Resources\SizingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSizings extends ManageRecords
{
    protected static string $resource = SizingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
