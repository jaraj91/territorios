<?php

namespace App\Actions;

use Illuminate\Support\Collection;

class CreateFormS13PDF
{
    public static function execute(array $programs, string $year)
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

        $results = collect($results);
        $results2 = $results->chunkWhile(function (array $value, int $key, Collection $chunk) {
            return $value['territory'] == $chunk->last()['territory'] && $chunk->count() < 4;
        });
        $results3 = $results2->chunk(20);

        return view('s13form', [
            'pages' => $results3,
            'year' => $year,
        ]);
    }
}
