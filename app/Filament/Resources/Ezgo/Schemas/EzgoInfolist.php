<?php

namespace App\Filament\Resources\Ezgo\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EzgoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Transaction Details')
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID'),

                        TextEntry::make('service_name')
                            ->label('Service'),

                        TextEntry::make('external_order_id')
                            ->label('External Order ID'),

                        TextEntry::make('transaction_id')
                            ->label('Transaction ID'),

                        TextEntry::make('reference_number')
                            ->label('Reference Number'),

                        TextEntry::make('customer_phone')
                            ->label('Customer Phone'),

                        TextEntry::make('amount')
                            ->label('Amount')
                            ->formatStateUsing(fn ($state) => number_format((float) $state, 2)),

                        TextEntry::make('type')
                            ->label('Payment Type')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'pending' => 'warning',
                                'success' => 'success',
                                'paid' => 'success',
                                'failed' => 'danger',
                                'cancelled' => 'gray',
                                default => 'gray',
                            }),

                        TextEntry::make('created_at')
                            ->label('Created At'),

                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->placeholder('—'),

                        TextEntry::make('paid_at')
                            ->label('Paid At')
                            ->placeholder('—'),
                    ])
                    ->columns(2),

                Section::make('Payloads')
                    ->schema([
                        TextEntry::make('request_payload')
                            ->label('Request Payload')
                            ->placeholder('NULL')
                            ->columnSpanFull(),

                        TextEntry::make('response_payload')
                            ->label('Response Payload')
                            ->placeholder('NULL')
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }
}
