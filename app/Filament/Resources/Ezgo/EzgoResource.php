<?php

namespace App\Filament\Resources\Ezgo;

use App\Filament\Resources\Ezgo\Pages\ListEzgo;
use App\Filament\Resources\Ezgo\Pages\ViewEzgo;
use App\Filament\Resources\Ezgo\Schemas\EzgoInfolist;
use App\Filament\Resources\Ezgo\Tables\EzgoTable;
use App\Models\Ezgo;
use App\Services\PaymentService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use App\Enums\UserRole;

class EzgoResource extends Resource
{
    protected static ?string $model = Ezgo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $navigationLabel = 'EzGo Transactions';

    protected static ?string $recordTitleAttribute = 'transaction_id';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        return $user?->isAdmin() || $user?->hasRole(UserRole::Ezgo);
    }

    public static function canViewAny(): bool
    {
    $user = auth()->user();

    return $user?->isAdmin() || $user?->hasRole(UserRole::Ezgo);
}

    public static function canCreate(): bool
{
    $user = auth()->user();

    return $user?->isAdmin() || $user?->hasRole(UserRole::Ezgo);
}

    public static function canEdit(Model $record): bool
{
    $user = auth()->user();

    return $user?->isAdmin() || $user?->hasRole(UserRole::Ezgo);
}

    public static function canDelete(Model $record): bool
{
    $user = auth()->user();

    return $user?->isAdmin() || $user?->hasRole(UserRole::Ezgo);
}

    public static function infolist(Schema $schema): Schema
    {
        return EzgoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EzgoTable::configure($table);
    }

    public static function resolveRecordRouteBinding(int|string $key, ?\Closure $modifyQuery = null): ?Model
    {
        $items = app(PaymentService::class)->getEzgoList();

        $item = collect($items)->firstWhere('id', $key);

        if (! $item) {
            return null;
        }

        $ezgo = Ezgo::fromApi($item);
        $ezgo->exists = true;

        return $ezgo;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEzgo::route('/'),
            'view' => ViewEzgo::route('/{record}'),
        ];
    }
}
