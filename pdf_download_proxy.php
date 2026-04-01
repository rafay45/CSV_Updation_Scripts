<?php
/**
 * PDF Download Proxy
 * Forces PDFs to download instead of opening in browser
 *
 * Add this to your theme directory and update the download links
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    // If not in WordPress context, require wp-load.php
    require_once('../../../wp-load.php');
}

// Get the PDF URL from query parameter
$pdf_url = isset($_GET['file']) ? $_GET['file'] : '';
$filename = isset($_GET['name']) ? $_GET['name'] : 'download';

// Validate URL
if (empty($pdf_url)) {
    wp_die('No file specified');
}

// Security: Only allow URLs from same domain
$site_url = site_url();
$parsed_url = parse_url($pdf_url);
$parsed_site = parse_url($site_url);

if ($parsed_url['host'] !== $parsed_site['host']) {
    wp_die('Invalid file URL');
}

// Get the file path from URL
$upload_dir = wp_upload_dir();
$base_url = $upload_dir['baseurl'];

// Convert URL to file path
$file_path = str_replace($base_url, $upload_dir['basedir'], $pdf_url);

// Remove query parameters from file path
$file_path = strtok($file_path, '?');

// Check if file exists
if (!file_exists($file_path)) {
    wp_die('File not found: ' . esc_html($filename));
}

// Check if it's a PDF
$file_info = pathinfo($file_path);
if (strtolower($file_info['extension']) !== 'pdf') {
    wp_die('Only PDF files are allowed');
}

// Clean filename
$clean_filename = sanitize_file_name($filename);
if (!preg_match('/\.pdf$/i', $clean_filename)) {
    $clean_filename .= '.pdf';
}

// Force download with proper headers
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $clean_filename . '"');
header('Content-Length: ' . filesize($file_path));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Clear output buffer
if (ob_get_level()) {
    ob_end_clean();
}

// Read and output file
readfile($file_path);
exit;
