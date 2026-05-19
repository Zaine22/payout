<?php

namespace App\Filament\Resources\Ezgo\Pages;

use App\Filament\Resources\Ezgo\EzgoResource;
use Filament\Resources\Pages\ListRecords;

class ListEzgo extends ListRecords
{
    protected static string $resource = EzgoResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
