@props([
    'recordsGroupByDate',
    'month' => 'ENERO',
    'year' => '2024',
    'bgPrimary' => 'bg-red-300',
    'bgSecondary' => 'bg-red-100'
])

<x-pdf.layout>
    <x-pdf.page class="border text-black {{ $bgSecondary }}">
        <header class="{{ $bgPrimary }}">
            <div class="text-center border border-amber-50">
                <h1 class="font-bold">PROGRAMA DE PREDICACIÓN {{ $month }} {{ $year }}</h1>
                <p class="italic">“Y las buenas noticias del Reino se predicarán en toda la tierra habitada [...]” (Mateo 24:14)</p>
            </div>
            <div class="grid grid-cols-7 justify-evenly text-center">
                <x-pdf.cell>FECHA</x-pdf.cell>
                <x-pdf.cell>DIA</x-pdf.cell>
                <x-pdf.cell>HORA</x-pdf.cell>
                <x-pdf.cell>GRUPOS</x-pdf.cell>
                <x-pdf.cell>LUGAR</x-pdf.cell>
                <x-pdf.cell>CAPITÁN</x-pdf.cell>
                <x-pdf.cell>TERRITORIOS</x-pdf.cell>
            </div>
        </header>
        <main>
            @foreach($recordsGroupByDate as $records)
                @php
                $dateCarbon = \Illuminate\Support\Carbon::make($records->first()->date);
                @endphp
                <div @class([$bgPrimary => $records->first()->is_highlight_day, "grid grid-cols-7 grid-rows-{$records->count() }"])>
                    <x-pdf.cell class="row-span-{{ $records->count() }}">
                        {{ $dateCarbon->format('d') }}
                    </x-pdf.cell>
                    <x-pdf.cell class="uppercase row-span-{{ $records->count() }}">
                        {{ $dateCarbon->dayName }}
                    </x-pdf.cell>
                    @foreach($records as $record)
                        @php
                            $hour = \Illuminate\Support\Carbon::make($record->date)->format('H:i');
                            $groups = $record->type;
                            $address = $record->address;
                            $captain = $record->captain;
                            $territory = $record->territory;
                        @endphp
                        <x-pdf.cell @class([$bgPrimary => $record->is_highlight_hour])>{{ $hour }}</x-pdf.cell>
                        <x-pdf.cell @class([$bgPrimary => $record->is_highlight_hour])>{{ $groups }}</x-pdf.cell>
                        <x-pdf.cell @class([$bgPrimary => $record->is_highlight_hour])>{{ $address }}</x-pdf.cell>
                        <x-pdf.cell @class([$bgPrimary => $record->is_highlight_hour])>{{ $captain }}</x-pdf.cell>
                        <x-pdf.cell @class([$bgPrimary => $record->is_highlight_hour])>{{ $territory }}</x-pdf.cell>
                    @endforeach
                </div>
            @endforeach
        </main>
    </x-pdf.page>
</x-pdf.layout>
