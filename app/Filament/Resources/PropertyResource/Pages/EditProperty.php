<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // 👇 Esta línea activa los tabs de relaciones
    protected function hasRelationManagersInTabs(): bool
    {
        return true;
    }

    // 👇 Esta asegura que los relation managers estén activos
    protected function hasRelationManagers(): bool
    {
        return true;
    }


}

