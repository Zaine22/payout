<?php

namespace App\Filament\Widgets;

use App\Services\PaymentService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EzgoStatsOverview extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->hasRole(\App\Enums\UserRole::Ezgo) ?? false;
    }

    protected function getStats(): array
    {
        $paymentService = app(PaymentService::class);

        // Get EZGO transaction statistics
        $ezgoList = $paymentService->getEzgoList();
        $totalTransactions = count($ezgoList);

        $pendingTransactions = collect($ezgoList)->where('status', 'pending')->count();
        $completedTransactions = collect($ezgoList)->where('status', 'completed')->count();
        $failedTransactions = collect($ezgoList)->where('status', 'failed')->count();

        $totalAmount = collect($ezgoList)->where('status', 'completed')->sum('amount');

        return [
            Stat::make('Total Transactions', $totalTransactions)
                ->description('All EZGO transactions')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('primary'),

            Stat::make('Pending', $pendingTransactions)
                ->description('Awaiting processing')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Completed', $completedTransactions)
                ->description('Successfully processed')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Failed', $failedTransactions)
                ->description('Failed transactions')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Total Amount', number_format($totalAmount, 2))
                ->description('Total transaction value')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),
        ];
    }
}
