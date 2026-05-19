<?php

namespace App\Filament\Resources\Ezgo\Pages;

use App\Filament\Resources\Ezgo\EzgoResource;
use Filament\Resources\Pages\ViewRecord;

class ViewEzgo extends ViewRecord
{
    protected static string $resource = EzgoResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
