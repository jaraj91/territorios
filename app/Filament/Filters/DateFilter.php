<?php

namespace App\Filament\Filters;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class DateFilter extends Filter
{
    public static function make(?string $name = null): static
    {
        $from = "{$name}_from";
        $until = "{$name}_until";

        return parent::make($name)
            ->form([
                DatePicker::make($from),
                DatePicker::make($until),
            ])
            ->query(function (Builder $query, array $data) use ($name, $from, $until): Builder {
                return $query
                    ->when(
                        $data[$from],
                        fn (Builder $query, $date): Builder => $query->whereDate($name, '>=', $date),
                    )
                    ->when(
                        $data[$until],
                        fn (Builder $query, $date): Builder => $query->whereDate($name, '<=', $date),
                    );
            });
    }
}
