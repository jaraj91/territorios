<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Collection;
use function Spatie\LaravelPdf\Support\pdf;
use Spatie\LaravelPdf\Enums\Format;

class CreateReportFolder
{
    public function __construct(
        private Collection $programs,
        private string $year,
        private CreateProgramPDF $programPDF,
        private CreateFormS13PDF $formPDF,
    ) {
    }

    public function execute()
    {
        $this->programPDF->execute($this->getProgramsId());
        $this->formPDF->execute($this->getProgramsId(), $this->year);
    }

    private function getProgramsId()
    {
        return $this->programs->pluck('id')->all();
    }
}
