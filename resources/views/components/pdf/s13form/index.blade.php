@props([
    'year',
    'records',
])

<x-pdf.layout>
    <x-pdf.page class="pt-9 pb-8 px-6">
        <h1 class="text-center font-medium text-lg">REGISTRO DE ASIGNACIÓN DE TERRITORIO</h1>
        <p class="mt-3 font-bold">Año de servicio: <span class="border-b px-4 border-black font-normal">{{ $year ?? '' }}</span></p>
        <header class="grid grid-cols-19 text-xs mt-3 bg-gray-200">
            <section class="grid grid-cols-subgrid col-span-3 border-2 border-black">
                <x-pdf.form_cell_terr>Num de terr.</x-pdf.form_cell_terr>
                <x-pdf.form_cell_last_date>Última fecha en que se completó*</x-pdf.form_cell_last_date>
            </section>
            <section class="grid grid-cols-subgrid border-2 border-l-0 border-black" style="grid-column: span 16 / span 16;">
                <x-pdf.form_cell />
                <x-pdf.form_cell />
                <x-pdf.form_cell />
                <x-pdf.form_cell />
            </section>
        </header>
        <main>
            @foreach($records->chunk(4) as $territories)
                @foreach($territories as $territory => $row)
                    @php
                    $lastIndex = $row->count() - 1;
                    if ($lastIndex !== 3) {
                        $blankItems = array_fill($lastIndex, 3 - $lastIndex, array_fill_keys(['captain', 'dateStart', 'dateEnd'], ''));
                        $row = [...$row, ...$blankItems];
                    }
                    @endphp
                    @foreach($row as $item)
                        @if($loop->first)
                            <div class="grid grid-cols-19 text-xs min-h-11">
                                <section class="grid grid-cols-subgrid col-span-3 border-2 border-t-0 border-black">
                                    <x-pdf.form_cell_terr>{{ $territory }}</x-pdf.form_cell_terr>
                                    <x-pdf.form_cell_last_date></x-pdf.form_cell_last_date>
                                </section>
                                <section class="grid grid-cols-subgrid border-2 border-l-0 border-t-0 border-black" style="grid-column: span 16 / span 16;">
                        @endif
                                    <x-pdf.form_cell :captain="$item['captain']" :dateStart="$item['dateStart']" :dateEnd="$item['dateEnd']" />
                        @if($loop->last)
                                </section>
                            </div>
                        @endif
                    @endforeach
                @endforeach
            @endforeach

            @for($i = 0; $i < 20 - $records->count(); $i++)
                <div class="grid grid-cols-19 text-xs min-h-11">
                    <section class="grid grid-cols-subgrid col-span-3 border-2 border-t-0 border-black">
                        <x-pdf.form_cell_terr></x-pdf.form_cell_terr>
                        <x-pdf.form_cell_last_date></x-pdf.form_cell_last_date>
                    </section>
                    <section class="grid grid-cols-subgrid border-2 border-l-0 border-t-0 border-black" style="grid-column: span 16 / span 16;">
                        <x-pdf.form_cell captain="" dateStart="" dateEnd="" />
                        <x-pdf.form_cell captain="" dateStart="" dateEnd="" />
                        <x-pdf.form_cell captain="" dateStart="" dateEnd="" />
                        <x-pdf.form_cell captain="" dateStart="" dateEnd="" />
                    </section>
                </div>
            @endfor
        </main>
        <footer>
            <p class="text-xs">*Cuando comience una nueva página, anote en esta columna la última fecha en que los territorios se completaron.</p>
            <p class="text-sm">S-13-S 1/22</p>
        </footer>
    </x-pdf.page>
</x-pdf.layout>
