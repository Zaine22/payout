<?php

namespace App\Filament\Widgets;

use App\Services\PaymentService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Enums\UserRole;

class AdminStatsOverview extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getStats(): array
    {
        $paymentService = app(PaymentService::class);

        // Get payment statistics
        $allPayments = $paymentService->list(-1, 1, 1000);
        $totalPayments = count($allPayments['items'] ?? []);

        $pendingPayments = collect($allPayments['items'] ?? [])->where('status', 0)->count();
        $completedPayments = collect($allPayments['items'] ?? [])->where('status', 1)->count();

        $totalAmount = collect($allPayments['items'] ?? [])->sum('money');

        // Get user statistics
        $totalUsers = User::count();
        $adminUsers = User::where('role', UserRole::Admin->value)->count();
        $ezgoUsers = User::where('role', UserRole::Ezgo->value)->count();

        return [
            Stat::make('Total Payments', $totalPayments)
                ->description('All payment requests')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Pending Payments', $pendingPayments)
                ->description('Awaiting approval')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Completed Payments', $completedPayments)
                ->description('Successfully processed')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Total Amount', number_format($totalAmount, 2))
                ->description('Total payment value')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),

            Stat::make('Total Users', $totalUsers)
                ->description("{$adminUsers} Admin, {$ezgoUsers} EZGO")
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}