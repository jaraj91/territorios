<?php

namespace App\Http\Controllers;

use App\Actions\CreateFormS13PDF;
use App\Actions\CreateProgramPDF;
use Illuminate\Http\Request;

class PDFController extends Controller
{
    public function program(Request $request)
    {
        return CreateProgramPDF::execute($request->get('ids', []));
    }

    public function s13form(Request $request)
    {
        return CreateFormS13PDF::execute($request->get('ids', []), $request->get('year', date('Y')));
    }
}
