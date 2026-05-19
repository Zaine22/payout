<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Ezgo = 'ezgo';
    case Ezwel = 'ezwel';
    case Merchant = 'merchant';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Ezgo => 'EZGO',
            self::Ezwel => 'EZWEL',
            self::Merchant => 'Merchant',
        };
    }
}