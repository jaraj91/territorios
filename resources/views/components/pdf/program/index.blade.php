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
            <div class="grid grid-cols-12 justify-evenly text-center">
                <x-pdf.cell>FECHA</x-pdf.cell>
                <x-pdf.cell class="col-span-2">DIA</x-pdf.cell>
                <x-pdf.cell>HORA</x-pdf.cell>
                <x-pdf.cell>GRUPOS</x-pdf.cell>
                <x-pdf.cell class="col-span-3">LUGAR</x-pdf.cell>
                <x-pdf.cell class="col-span-2">CAPITÁN</x-pdf.cell>
                <x-pdf.cell class="col-span-2">TERRITORIOS</x-pdf.cell>
            </div>
        </header>
        <main>
            @foreach($recordsGroupByDate as $records)
                @php
                $dateCarbon = \Illuminate\Support\Carbon::make($records->first()->date);
                $hour = $dateCarbon->format('H:i');
                $rowSpan = "row-span-{$records->count()}";
                @endphp
                <div @class([$bgPrimary => $records->first()->is_highlight_day, "grid grid-cols-12 grid-rows-{$records->count() }"])>
                    <x-pdf.cell class="row-span-{{ $records->count() }}">
                        {{ $dateCarbon->format('d') }}
                    </x-pdf.cell>
                    <x-pdf.cell class="uppercase col-span-2 row-span-{{ $records->count() }}">
                        {{ $dateCarbon->dayName }}
                    </x-pdf.cell>
                    <x-pdf.cell @class([$bgPrimary => $records->first()->is_highlight_hour, $rowSpan])>{{ $hour }}</x-pdf.cell>
                    @foreach($records as $record)
                        @php
                            $groups = $record->type;
                            $address = $record->address;
                            $captain = $record->captain;
                            $territory = $record->territory;
                        @endphp
                        <x-pdf.cell @class([$bgPrimary => $record->is_highlight_hour])>{{ $groups }}</x-pdf.cell>
                        <x-pdf.cell @class([$bgPrimary => $record->is_highlight_hour, 'col-span-3'])>{{ $address }}</x-pdf.cell>
                        <x-pdf.cell @class([$bgPrimary => $record->is_highlight_hour, 'col-span-2'])>{{ $captain }}</x-pdf.cell>
                        <x-pdf.cell @class([$bgPrimary => $record->is_highlight_hour, 'col-span-2'])>{{ $territory }}</x-pdf.cell>
                    @endforeach
                </div>
            @endforeach
        </main>
    </x-pdf.page>
</x-pdf.layout>
