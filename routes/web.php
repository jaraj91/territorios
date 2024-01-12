<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPdf\Enums\Format;
use function Spatie\LaravelPdf\Support\pdf;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/program', function () {
    $records = DB::table('groups')
        ->select(DB::raw("groups.date, groups.type, groups.is_highlight_day, groups.is_highlight_hour, addresses.address, captains.name as captain, GROUP_CONCAT(territories.name SEPARATOR ' - ') territory"))
        ->join('addresses', 'groups.address_id', '=', 'addresses.id')
        ->join('captains', 'groups.captain_id', '=', 'captains.id')
        ->join('territories', 'groups.territory_id', '=', 'territories.id')
        ->groupBy('date', 'type', 'addresses.address', 'captains.name', 'groups.is_highlight_day', 'groups.is_highlight_hour')
        ->get()
        ->groupBy(fn ($item) => \Illuminate\Support\Carbon::make($item->date)?->format('Y-m-d'));

    return view('program', compact('records'));
});

Route::get('/s13form', function () {
    $records = \App\Models\Group::with(['captain', 'territory'])
        ->where('progress', '!=', '[]')
        ->where('progress', 'IS NOT', null)
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

    return view('s13form', ['pages' => $results]);
});
