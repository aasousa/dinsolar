<?php

namespace App\Filament\Admin\Resources\PanelResource\Pages;

use App\Filament\Admin\Resources\PanelResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePanels extends ManageRecords
{
    protected static string $resource = PanelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
