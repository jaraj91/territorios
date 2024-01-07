<?php

namespace App\Enums;

enum Months: int
{
    case January = 1;
    case February = 2;
    case March = 3;
    case April = 4;
    case May = 5;
    case June = 6;
    case July = 7;
    case August = 8;
    case September = 9;
    case October = 10;
    case November = 11;
    case December = 12;

    public static function list(): array
    {
        return array_combine(
            array_map(fn ($item) => $item->value, self::cases()),
            array_map(fn ($item) => $item->label(), self::cases())
        );
    }

    public function label(): string
    {
        return match($this) {
            self::January => 'Enero',
            self::February => 'Febrero',
            self::March => 'Marzo',
            self::April => 'Abril',
            self::May => 'Mayo',
            self::June => 'Junio',
            self::July => 'Julio',
            self::August => 'Agosto',
            self::September => 'Septiembre',
            self::October => 'Octubre',
            self::November => 'Noviembre',
            self::December => 'Diciembre'
        };
    }
}
