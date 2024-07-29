<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\PDF;

class PDFController extends Controller
{
    /*public function generatePDF()
    {
        $data = [
            'hoy' => date('d/m/Y')
        ];

        $pdf = PDF::loadView('pdf.mi_vista_pdf', $data);

        return $pdf->download('archivo.pdf');
    }*/
}