<?php

namespace App\Filament\Resources\GroupResource\Pages;

use App\Filament\Resources\GroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageGroups extends ManageRecords
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data, string $model): Model {
                    $territories = $data['territories'];
                    unset($data['territories']);
                    foreach ($territories as $territory) {
                        $data['territory_id'] = $territory;
                        $lastObject = $model::create($data);
                    }
                    return $lastObject;
                }),
        ];
    }
}
