<?php

namespace App\Actions;

use function Spatie\LaravelPdf\Support\pdf;
use Spatie\LaravelPdf\Enums\Format;

class CreateFormS13PDF
{
    public function execute(array $programs, string $year)
    {
        $records = \App\Models\Group::with(['captain', 'territory'])
            ->where('progress', '!=', '[]')
            ->where('progress', 'IS NOT', null)
            ->whereIn('program_id', $programs)
            ->get()
            ->groupBy([
                fn ($item) => $item->territory->name,
            ])
            ->sortBy(fn($value, $key) => (int) $key);

        $results = [];

        foreach ($records as $record) {
            $pendingAccumulated = [];
            $captain = '';
            $dateStart = '';
            foreach ($record as $item) {
                if (empty($pendingAccumulated)) {
                    $captain = $item->captain->name;
                    $dateStart = $item->date->format('d-m-Y');
                    $pendingAccumulated = $item->pending;
                } else {
                    $pendingAccumulated = array_diff($pendingAccumulated, $item->progress);
                }

                if (empty($pendingAccumulated)) {
                    $results[] = [
                        'territory' => $item->territory->name,
                        'captain' => $captain,
                        'dateStart' => $dateStart,
                        'dateEnd' => $item->date->format('d-m-Y'),
                    ];
                }
            }
        }

        $results = collect($results)->groupBy('territory')->chunk(20);

        return pdf()
            ->view('s13form', [
                'pages' => $results,
                'year' => $year,
            ])
            ->format(Format::A4)
            ->disk('local')
            ->save('formularios_s13.pdf');
    }
}
