<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Models\Payment;
use App\Services\PaymentService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('id')
                    ->label('ID'),

                TextColumn::make('name')
                    ->label('Account Name'),

                ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular()
                    ->height(40)
                    ->width(40),

                TextColumn::make('mobile')
                    ->label('Phone'),

                TextColumn::make('money')
                    ->label('MMK'),

                TextColumn::make('account_bank')
                    ->label('Bank'),

                TextColumn::make('account')
                    ->label('Account'),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => fn ($state) => (int) $state === 0,
                        'success' => fn ($state) => (int) $state === 1,
                        'danger' => fn ($state) => (int) $state === 2,
                    ])
                    ->formatStateUsing(function ($state) {

                        return match ((int) $state) {
                            0 => 'Pending',
                            1 => 'Paid',
                            2 => 'Rejected',
                            default => 'Unknown',
                        };

                    }),

                TextColumn::make('addtime')
                    ->label('Requested At')
                    ->formatStateUsing(function ($state) {

                        if (! $state) {
                            return '-';
                        }

                        return \Carbon\Carbon::createFromTimestamp((int) $state)
                            ->format('Y-m-d H:i');

                    }),

            ])

            ->actions([

                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->visible(fn ($record) => $record->status == 0)
                    ->requiresConfirmation()
                    ->action(function ($record) {

                        $success = app(PaymentService::class)
                            ->updateStatus($record->id, 1);

                        if (! $success) {
                            Notification::make()
                                ->title('Failed to approve payout')
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Payout approved')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status == 0)
                    ->requiresConfirmation()
                    ->action(function ($record) {

                        $success = app(PaymentService::class)
                            ->updateStatus($record->id, 2);

                        if (! $success) {
                            Notification::make()
                                ->title('Failed to reject payout')
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Payout rejected')
                            ->success()
                            ->send();
                    }),

            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        0 => 'Pending',
                        1 => 'Paid',
                        2 => 'Rejected',
                    ]),
            ])

            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])

            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ])

            ->records(fn ($livewire) => static::getApiData($livewire));
    }

    // protected static function getApiData(): Collection
    // {
    //     try {

    //         $items = app(PaymentService::class)->list();

    //         return collect($items)
    //             ->map(fn($i) => Payment::fromApi($i));

    //     } catch (\Throwable $e) {

    //         report($e);

    //         return collect();
    //     }
    // }

    protected static function getApiData($livewire): LengthAwarePaginator
    {
        try {
            $filters = $livewire->tableFilters ?? [];

            $status = $filters['status']['value'] ?? null;

            if ($status !== null && $status !== '') {
                $status = (int) $status;
            } else {
                $status = null;
            }

            $page = method_exists($livewire, 'getTablePage')
                ? (int) $livewire->getTablePage()
                : 1;

            $perPage = method_exists($livewire, 'getTableRecordsPerPage')
                ? $livewire->getTableRecordsPerPage()
                : 10;

            if ($perPage === 'all') {
                $perPage = 100;
            }

            $perPage = (int) $perPage;

            $result = app(PaymentService::class)->list(
                status: $status,
                page: $page,
                perPage: $perPage,
            );

            $items = collect($result['items'] ?? [])
                ->map(fn ($i) => Payment::fromApi($i));

            $meta = $result['meta'] ?? [];

            return new LengthAwarePaginator(
                $items,
                $meta['total'] ?? $items->count(),
                $meta['per_page'] ?? $perPage,
                $meta['current_page'] ?? $page,
                [
                    'path' => request()->url(),
                    'query' => request()->query(),
                ]
            );

        } catch (\Throwable $e) {
            report($e);

            return new LengthAwarePaginator(
                collect(),
                0,
                10,
                1,
                [
                    'path' => request()->url(),
                    'query' => request()->query(),
                ]
            );
        }
    }
}
