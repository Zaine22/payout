<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->schema([
                        ImageEntry::make('avatar')
                            ->label('Avatar')
                            ->circular()
                            ->height(80)
                            ->width(80),

                        TextEntry::make('user_nickname')
                            ->label('User Name'),

                        TextEntry::make('mobile')
                            ->label('Phone'),
                    ])
                    ->columns(3),

                Section::make('Payout Information')
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID'),

                        TextEntry::make('orderno')
                            ->label('Order No'),

                        TextEntry::make('money')
                            ->label('MMK')
                            ->formatStateUsing(fn ($state) => number_format((float) $state, 2)),

                        TextEntry::make('account_bank')
                            ->label('Bank'),

                        TextEntry::make('account')
                            ->label('Account'),

                        TextEntry::make('name')
                            ->label('Account Name'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn ($state) => match ((int) $state) {
                                0 => 'warning',
                                1 => 'success',
                                2 => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => match ((int) $state) {
                                0 => 'Pending',
                                1 => 'Paid',
                                2 => 'Rejected',
                                default => 'Unknown',
                            }),

                        TextEntry::make('addtime')
                            ->label('Requested At')
                            ->formatStateUsing(function ($state) {
                                if (! $state) {
                                    return '-';
                                }

                                return \Carbon\Carbon::createFromTimestamp((int) $state)
                                    ->format('Y-m-d H:i');
                            }),
                    ])
                    ->columns(2),
            ]);
    }
}
