<?php

require_once __DIR__ . "/../vendor/dompdf/autoload.inc.php";

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfReportGenerator
{
    // Convierte el HTML en un PDF A4 y lo envía al navegador
    public function renderPdf($htmlTemplate, $filename)
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans'); // soporta tildes y ñ
        $options->set('isRemoteEnabled', false);     // no descarga recursos de internet

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($htmlTemplate);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($filename, ['Attachment' => false]); // false = abre en el navegador
    }
}