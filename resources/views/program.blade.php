<x-pdf.layout>
    @foreach($pages as $records)
        <x-pdf.program :recordsGroupByDate="$records"/>
        @if(! $loop->last)
            @pageBreak
        @endif
    @endforeach
</x-pdf.layout>
