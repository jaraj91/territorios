<?php

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
    $records = \App\Models\Group::with(['address', 'captain', 'territory'])->get()->groupBy([
        fn ($item): string => $item->date->format('Y-m-d'),
    ]);

    return view('program', compact('records'))
//        ->disk('public')
//        ->save('programa.pdf');
    ;
});
