<?php

namespace App\Filament\Widgets;

use App\Services\PaymentService;
use App\Models\Payment;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;

class RecentPaymentsWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Payments';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('orderno')
                    ->label('Order No')
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),

                TextColumn::make('money')
                    ->label('Amount (MMK)')
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),

                TextColumn::make('account_bank')
                    ->label('Bank'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ((int) $state) {
                        0 => 'Pending',
                        1 => 'Paid',
                        2 => 'Rejected',
                        default => 'Unknown',
                    })
                    ->colors([
                        'warning' => fn ($state) => (int) $state === 0,
                        'success' => fn ($state) => (int) $state === 1,
                        'danger' => fn ($state) => (int) $state === 2,
                    ]),

                TextColumn::make('addtime')
                    ->label('Date')
                    ->formatStateUsing(function ($state) {
                        if (!$state) {
                            return '-';
                        }
                        return \Carbon\Carbon::createFromTimestamp((int) $state)
                            ->format('Y-m-d H:i');
                    }),
            ])
            ->records(fn () => $this->getPayments())
            ->paginated(false);
    }

    protected function getPayments()
    {
        try {
            $paymentService = app(PaymentService::class);
            $result = $paymentService->list(null, 1, 10);

            return collect($result['items'] ?? [])
                ->map(fn ($i) => Payment::fromApi($i));

        } catch (\Throwable $e) {
            report($e);
            return collect();
        }
    }
}