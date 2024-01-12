<x-pdf.layout>
    @foreach($pages as $records)
        <x-pdf.s13form :records="$records" :year="$year"></x-pdf.s13form>
        @if(! $loop->last)
            @pageBreak
        @endif
    @endforeach
</x-pdf.layout>
