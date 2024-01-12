@props([
    'recordsGroupByDate',
])

@php
$bgPrimary = $recordsGroupByDate->first()->first()->bg_primary ?? 'bg-red-300';
$bgSecondary = $recordsGroupByDate->first()->first()->bg_secondary ?? 'bg-red-100';
$comment = $recordsGroupByDate->first()->first()->comment ?? '';
@endphp
<x-pdf.layout>
    <x-pdf.page class="text-black border-0">
        <header class="{{ $bgPrimary }}">
            <div class="text-center border border-amber-50">
                <h1 class="font-bold uppercase">PROGRAMA DE PREDICACIÓN {{ \Illuminate\Support\Carbon::make($recordsGroupByDate->first()->first()->date)->monthName }} {{ \Illuminate\Support\Carbon::make($recordsGroupByDate->first()->first()->date)->year }}</h1>
                <p class="italic">“Y las buenas noticias del Reino se predicarán en toda la tierra habitada [...]” (Mateo 24:14)</p>
            </div>
            <div class="grid grid-cols-12 justify-evenly text-center">
                <x-pdf.program_cell>FECHA</x-pdf.program_cell>
                <x-pdf.program_cell class="col-span-2">DIA</x-pdf.program_cell>
                <x-pdf.program_cell>HORA</x-pdf.program_cell>
                <x-pdf.program_cell>GRUPOS</x-pdf.program_cell>
                <x-pdf.program_cell class="col-span-3">LUGAR</x-pdf.program_cell>
                <x-pdf.program_cell class="col-span-2">CAPITÁN</x-pdf.program_cell>
                <x-pdf.program_cell class="col-span-2">TERRITORIOS</x-pdf.program_cell>
            </div>
        </header>
        <main class="{{ $bgSecondary }}">
            @foreach($recordsGroupByDate as $records)
                @php
                $dateCarbon = \Illuminate\Support\Carbon::make($records->first()->date);
                $hour = $dateCarbon->format('H:i');
                $rowSpan = "row-span-{$records->count()}";
                @endphp
                <div @class([$bgPrimary => $records->first()->is_highlight_day, "grid grid-cols-12 grid-rows-{$records->count() }"])>
                    <x-pdf.program_cell class="row-span-{{ $records->count() }}">
                        {{ $dateCarbon->format('d') }}
                    </x-pdf.program_cell>
                    <x-pdf.program_cell class="uppercase col-span-2 row-span-{{ $records->count() }}">
                        {{ $dateCarbon->dayName }}
                    </x-pdf.program_cell>
                    <x-pdf.program_cell @class([$bgPrimary => $records->first()->is_highlight_hour, $rowSpan])>{{ $hour }}</x-pdf.program_cell>
                    @foreach($records as $record)
                        @php
                            $groups = $record->type;
                            $address = $record->address;
                            $captain = $record->captain;
                            $territory = $record->territory;
                        @endphp
                        <x-pdf.program_cell @class([$bgPrimary => $record->is_highlight_hour])>{{ $groups }}</x-pdf.program_cell>
                        <x-pdf.program_cell @class([$bgPrimary => $record->is_highlight_hour, 'col-span-3'])>{{ $address }}</x-pdf.program_cell>
                        <x-pdf.program_cell @class([$bgPrimary => $record->is_highlight_hour, 'col-span-2'])>{{ $captain }}</x-pdf.program_cell>
                        <x-pdf.program_cell @class([$bgPrimary => $record->is_highlight_hour, 'col-span-2'])>{{ $territory }}</x-pdf.program_cell>
                    @endforeach
                </div>
            @endforeach
        </main>
        @if (! empty($comment))
        <footer class="mt-8 py-4 text-center font-bold {{ $bgPrimary }}">
            {{ $comment }}
        </footer>
        @endif
    </x-pdf.page>
</x-pdf.layout>
