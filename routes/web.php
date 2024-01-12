<?php

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

Route::get('/', function () {
//    $records = \App\Models\Group::with(['address', 'captain', 'territory'])->get()->groupBy([
//        fn ($item): string => $item->date->format('Y-m-d'),
//    ]);

    $records = DB::table('groups')
        ->select(DB::raw("groups.date, groups.type, groups.is_highlight_day, groups.is_highlight_hour, addresses.address, captains.name as captain, GROUP_CONCAT(territories.name SEPARATOR ' - ') territory"))
        ->join('addresses', 'groups.address_id', '=', 'addresses.id')
        ->join('captains', 'groups.captain_id', '=', 'captains.id')
        ->join('territories', 'groups.territory_id', '=', 'territories.id')
        ->groupBy('date', 'type', 'addresses.address', 'captains.name', 'groups.is_highlight_day', 'groups.is_highlight_hour')
        ->get()
        ->groupBy(fn ($item) => \Illuminate\Support\Carbon::make($item->date)?->format('Y-m-d'));

    return view('program', compact('records'))
//        ->disk('public')
//        ->save('programa.pdf');
    ;
});
