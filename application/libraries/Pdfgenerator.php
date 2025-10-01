<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';  // Autoload Composer dependencies

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdfgenerator {

  public function generate($html, $filename='', $stream=TRUE, $paper = 'A4', $orientation = "portrait")
  {
    // Initialize dompdf with options
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);  // Enable HTML5 support
    $options->set('isPhpEnabled', true);  // Enable PHP (if needed)
    $dompdf = new Dompdf($options);

    // Load the HTML content
    $dompdf->loadHtml($html);

    // Set the paper size and orientation
    $dompdf->setPaper($paper, $orientation);

    // Render the PDF (first pass)
    $dompdf->render();

    // Output the generated PDF
    if ($stream) {
        // Stream the PDF to the browser
        $dompdf->stream($filename . ".pdf", array("Attachment" => 0));
    } else {
        // Return the PDF content (if not streaming)
        return $dompdf->output();
    }
  }
}
