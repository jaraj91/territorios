<?php

namespace App\Enums;

enum GroupStatus: string
{
    case Pending = 'Pendiente';
    case Registered = 'Registrado';

    public static function colors(): array
    {
        return array_map(fn ($item) => $item->color(), self::cases());
    }

    public function color(): string
    {
        return match($this) {
            self::Pending => 'gray',
            self::Registered => 'success',
            default => 'gray',
        };
    }
}
