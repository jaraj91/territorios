<?php

namespace App\Filament\Resources\CaptainResource\Pages;

use App\Filament\Resources\CaptainResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCaptains extends ManageRecords
{
    protected static string $resource = CaptainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
