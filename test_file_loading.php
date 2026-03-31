<?php
/**
 * SIMPLE TEST FILE
 * Just add this line to functions.php:
 * require_once get_stylesheet_directory() . '/test_file_loading.php';
 */

if (!defined('ABSPATH')) exit;

// This will show at the TOP of every page if file loads
add_action('wp_head', function() {
    echo '<!-- WSF TEST FILE LOADED SUCCESSFULLY -->';
}, 1);

// This will log to debug.log
error_log('========== WSF TEST FILE LOADED ==========');
