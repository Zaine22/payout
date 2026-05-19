<?php

namespace App\Filament\Resources\Ezgo\Tables;

use App\Models\Ezgo;
use App\Services\PaymentService;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EzgoTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('external_order_id')
                    ->label('External Order ID'),

                TextColumn::make('transaction_id')
                    ->label('Transaction ID'),

                TextColumn::make('reference_number')
                    ->label('Reference No'),

                TextColumn::make('customer_phone')
                    ->label('Phone'),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2)),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color('info'),

                TextColumn::make('status')
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

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('Y-m-d H:i'),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'cancelled' => 'Cancelled',
                    ]),

                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'aya_qr' => 'AYA QR',
                        'kbz_qr' => 'KBZ QR',
                        'cb_qr' => 'CB QR',
                        'mpu' => 'MPU',
                    ]),
            ])

            ->defaultSort('id', 'desc')

            ->recordActions([
                ViewAction::make(),
            ])

            ->toolbarActions([])

            ->records(fn ($livewire) => static::getApiData($livewire));
    }

    protected static function getApiData(mixed $livewire): \Illuminate\Support\Collection
    {
        try {
            $items = app(PaymentService::class)->getEzgoList();

            $filters = $livewire->tableFilters ?? [];

            $statusFilter = $filters['status']['value'] ?? null;
            $typeFilter = $filters['type']['value'] ?? null;

            return collect($items)
                ->map(fn ($i) => Ezgo::fromApi($i))
                ->when(
                    ! empty($statusFilter),
                    fn ($col) => $col->where('status', $statusFilter)
                )
                ->when(
                    ! empty($typeFilter),
                    fn ($col) => $col->where('type', $typeFilter)
                );
        } catch (\Throwable $e) {
            report($e);

            return collect();
        }
    }
}
