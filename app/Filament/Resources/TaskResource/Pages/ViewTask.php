<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Filament\Resources\TaskResource\RelationManagers\SubtasksRelationManager;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getRelationManagers(): array
    {
        return [
            SubtasksRelationManager::class
        ];
    }
}
