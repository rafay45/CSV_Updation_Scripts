<?php
/**
 * Universal PDF Download Proxy
 * Forces ANY PDF URL to download instead of opening in browser
 *
 * Usage: download-pdf.php?url=PDF_URL&name=FILENAME
 */

// Get parameters
$pdf_url = isset($_GET['url']) ? $_GET['url'] : '';
$filename = isset($_GET['name']) ? $_GET['name'] : 'download';

// Validate
if (empty($pdf_url)) {
    die('Error: No PDF URL provided');
}

// Clean filename
$filename = preg_replace('/[^a-zA-Z0-9_-]/', '', $filename);
if (!preg_match('/\.pdf$/i', $filename)) {
    $filename .= '.pdf';
}

// Set headers to force download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Fetch and output the PDF
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'User-Agent: Mozilla/5.0'
    ]
]);

$pdf_content = @file_get_contents($pdf_url, false, $context);

if ($pdf_content === false) {
    die('Error: Could not fetch PDF from URL');
}

echo $pdf_content;
exit;
