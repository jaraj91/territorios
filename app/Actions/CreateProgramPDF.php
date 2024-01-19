<?php

namespace App\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CreateProgramPDF
{
    public static function execute(array $programs)
    {
        $pages = DB::table('groups')
            ->select(DB::raw("programs.bg_primary, programs.bg_secondary, programs.comment, groups.date, groups.type, groups.is_highlight_day, groups.is_highlight_hour, addresses.address, captains.name as captain, GROUP_CONCAT(territories.name SEPARATOR ' - ') territory"))
            ->join('addresses', 'groups.address_id', '=', 'addresses.id')
            ->join('captains', 'groups.captain_id', '=', 'captains.id')
            ->join('territories', 'groups.territory_id', '=', 'territories.id')
            ->join('programs', 'groups.program_id', '=', 'programs.id')
            ->whereIn('groups.program_id', $programs)
            ->orderBy('date')
            ->groupBy('programs.bg_primary', 'programs.bg_secondary', 'programs.comment', 'date', 'type', 'addresses.address', 'captains.name', 'groups.is_highlight_day', 'groups.is_highlight_hour')
            ->get()
            ->groupBy([
                fn ($item) => Carbon::make($item->date)?->format('Y-m'),
                fn ($item) => Carbon::make($item->date)?->format('Y-m-d'),
            ]);

        return view('program', compact('pages'));
    }
}
