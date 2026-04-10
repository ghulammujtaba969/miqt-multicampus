<?php
/**
 * PdfService: HTML -> PDF
 * - Prefers mPDF (Composer)
 * - Falls back to Dompdf (libs/dompdf) or TCPDF (libs/tcpdf)
 * - Handles UTF-8 + RTL (Urdu/Arabic)
 */

class PdfService {
    public static function outputHtml($html, $filename = 'report.pdf', $orientation = 'P') {
        // Attempt to load composer autoload (project/vendor or parent/vendor)
        $autoload1 = __DIR__ . '/../vendor/autoload.php';
        $autoload2 = __DIR__ . '/../../vendor/autoload.php';
        if (file_exists($autoload1)) require_once $autoload1;
        elseif (file_exists($autoload2)) require_once $autoload2;

        // 1) Try mPDF (preferred)
        if (class_exists('Mpdf\\Mpdf')) {
            $config = [
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => ($orientation === 'L') ? 'L' : 'P',
                'autoScriptToLang' => true,
                'autoLangToFont' => true,
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 12,
                'margin_bottom' => 12,
            ];

            $mpdf = new \Mpdf\Mpdf($config);
            $css = 'body{font-family:dejavusans, sans-serif; font-size:11px;}'
                 .'h1,h2,h3{margin:0 0 8px 0;}'
                 .'table{border-collapse:collapse;width:100%;}'
                 .'th,td{border:1px solid #444;padding:6px;}'
                 .'th{background:#e6ebe9;}'
                 .'.meta{margin-bottom:10px;}';
            $mpdf->WriteHTML('<style>'.$css.'</style>', \Mpdf\HTMLParserMode::HEADER_CSS);
            $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
            $mpdf->Output($filename, 'I');
            exit;
        }

        // 2) Try Dompdf (manual install or via composer)
        $dompdfAutoload = __DIR__ . '/dompdf/autoload.inc.php'; // libs/dompdf
        if (file_exists($dompdfAutoload)) {
            require_once $dompdfAutoload;
        }
        if (class_exists('Dompdf\\Dompdf')) {
            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', true);
            // DejaVu Sans covers most Unicode (Urdu/Arabic included)
            $options->set('defaultFont', 'DejaVu Sans');
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', ($orientation === 'L') ? 'landscape' : 'portrait');
            $dompdf->render();
            $dompdf->stream($filename, ['Attachment' => false]);
            exit;
        }

        // 3) Try TCPDF (manual install under libs/tcpdf)
        $tcpdfPath = __DIR__ . '/tcpdf/tcpdf.php';
        if (file_exists($tcpdfPath)) {
            require_once $tcpdfPath;
            $pdf = new \TCPDF(($orientation === 'L') ? 'L' : 'P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(10, 12, 10);
            $pdf->AddPage();
            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->Output($filename, 'I');
            exit;
        }

        // Nothing available: show guidance
        header('Content-Type: text/html; charset=utf-8');
        echo '<div style="padding:16px;font-family:Arial">'
            .'<h3>No PDF library found</h3>'
            .'<p>You can enable PDF export with any one of these:</p>'
            .'<ol>'
            .'<li><b>mPDF (Composer)</b>: <code>composer require mpdf/mpdf</code></li>'
            .'<li><b>Dompdf (manual)</b>: unzip into <code>libs/dompdf</code> so <code>libs/dompdf/autoload.inc.php</code> exists.</li>'
            .'<li><b>TCPDF (manual)</b>: unzip into <code>libs/tcpdf</code> so <code>libs/tcpdf/tcpdf.php</code> exists.</li>'
            .'</ol>'
            .'<p>After installing one, re-run the export.</p>'
            .'</div>';
        exit;
    }
}

