<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Collection;
use function Spatie\LaravelPdf\Support\pdf;
use Spatie\LaravelPdf\Enums\Format;

class CreateReportFolder
{
    public function __construct(
        private Collection $programs,
        private CreateProgramPDF $programPDF,
        private CreateFormS13PDF $formPDF,
    ) {
    }

    public function execute()
    {
        $this->programPDF->execute();
        $this->formPDF->execute();

        return pdf()
            ->format(Format::A4)
            ->view('programs_pdf')
            ->disk('public')
            ->save('programa.pdf');
    }
}
