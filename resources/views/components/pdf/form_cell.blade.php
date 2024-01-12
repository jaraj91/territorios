@props([
    'captain' => 'Asignado a',
    'dateStart' => 'Fecha en que se asignó',
    'dateEnd' => 'Fecha en que se completo',
])

<div {{ $attributes->merge(['class' => 'grid grid-cols-subgrid col-span-4']) }}>
    <div class="col-span-4 flex justify-center items-center text-center border-b border-r border-black" style="min-height: 0.4rem">{{ $captain }}</div>
    <div class="col-span-2 flex justify-center items-center text-center border-r border-black text-xxs">{{ $dateStart }}</div>
    <div class="col-span-2 flex justify-center items-center text-center border-r border-black text-xxs">{{ $dateEnd }}</div>
</div>
