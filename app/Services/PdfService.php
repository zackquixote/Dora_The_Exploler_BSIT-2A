<?php
namespace App\Services;

/**
 * PdfService – Generates PDF documents from HTML content.
 * Uses dompdf library. Install via: composer require dompdf/dompdf
 */
class PdfService
{
    /**
     * Generate a PDF from HTML string.
     *
     * @param string $html Full HTML markup to render.
     * @param string $orientation 'portrait' or 'landscape'
     * @return string Binary PDF data.
     */
    public function generate(string $html, string $orientation = 'portrait'): string
    {
        // Check if dompdf is available
        if (!class_exists('\\Dompdf\\Dompdf')) {
            throw new \RuntimeException('dompdf is not installed. Run: composer require dompdf/dompdf');
        }

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Inter');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', $orientation);
        $dompdf->render();

        return $dompdf->output();
    }

    /**
     * Generate PDF for a single ID card.
     */
    public function generateIdCard(array $resident, string $qrUrl): string
    {
        $html = view('id_generator/print', [
            'title'    => 'Print Barangay ID',
            'resident' => $resident,
            'qr_url'   => $qrUrl,
            'is_pdf'   => true,
        ]);
        return $this->generate($html);
    }
}
