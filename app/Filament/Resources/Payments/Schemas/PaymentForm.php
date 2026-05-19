<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Update Payout Status')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                0 => 'Pending',
                                1 => 'Paid',
                                2 => 'Rejected',
                            ])
                            ->required()
                            ->native(false),
                    ])
                    ->columns(1),
            ]);
    }
}
