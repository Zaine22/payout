<?php

namespace App\Filament\Widgets;

use App\Services\PaymentService;
use App\Models\Ezgo;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;

class RecentEzgoTransactionsWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Recent EZGO Transactions';

    public static function canView(): bool
    {
        return auth()->user()?->hasRole(\App\Enums\UserRole::Ezgo) ?? false;
    }

    public function table(Table $table): Table
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
            ->records(fn () => $this->getTransactions())
            ->paginated(false);
    }

    protected function getTransactions()
    {
        try {
            $paymentService = app(PaymentService::class);
            $items = $paymentService->getEzgoList();

            return collect($items)
                ->take(10)
                ->map(fn ($i) => Ezgo::fromApi($i));

        } catch (\Throwable $e) {
            report($e);
            return collect();
        }
    }
}
