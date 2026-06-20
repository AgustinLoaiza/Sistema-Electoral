<?php

namespace Tests\Unit;

use App\Support\PdfReport;
use Tests\TestCase;

class PdfReportTest extends TestCase
{
    public function test_it_generates_a_valid_pdf_document(): void
    {
        $pdf = PdfReport::make(
            'Reporte de prueba',
            'Subtitulo',
            ['Columna'],
            [['Valor']]
        );

        $this->assertStringStartsWith('%PDF-1.4', $pdf);
        $this->assertStringContainsString('%%EOF', $pdf);
    }
}
