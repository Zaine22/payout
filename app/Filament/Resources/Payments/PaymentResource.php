<?php

namespace App\Filament\Resources\Payments;

use App\Filament\Resources\Payments\Pages\EditPayment;
use App\Filament\Resources\Payments\Pages\ListPayments;
use App\Filament\Resources\Payments\Pages\ViewPayment;
use App\Filament\Resources\Payments\Schemas\PaymentForm;
use App\Filament\Resources\Payments\Schemas\PaymentInfolist;
use App\Filament\Resources\Payments\Tables\PaymentsTable;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Enums\UserRole;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Payment';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        // Only Admin can see Payment resource
        return $user?->isAdmin();
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user?->isAdmin();
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        return $user?->isAdmin();
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        return $user?->isAdmin();
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();

        return $user?->isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return PaymentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PaymentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentsTable::configure($table);
    }

    public static function resolveRecordRouteBinding(int|string $key, ?\Closure $modifyQuery = null): ?Model
    {
        $data = app(PaymentService::class)->find((int) $key);

        if (! $data) {
            return null;
        }

        $payment = Payment::fromApi($data);
        $payment->exists = true;

        return $payment;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
            'view' => ViewPayment::route('/{record}'),
            'edit' => EditPayment::route('/{record}/edit'),
        ];
    }
}
