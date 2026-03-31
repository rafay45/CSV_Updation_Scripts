<?php
/**
 * DIRECT OUTPUT TEST
 * Add to functions.php:
 * require_once get_stylesheet_directory() . '/test_direct_output.php';
 */

if (!defined('ABSPATH')) exit;

// Add HTML directly to wp_footer to test if ANY output works
add_action('wp_footer', function() {
    if (!is_product_category()) return;

    ?>
    <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: red; color: white; padding: 50px; z-index: 99999; font-size: 30px; border: 10px solid yellow;">
        ✅ TEST FILE WORKING! ACCORDIONS SHOULD APPEAR BELOW!
    </div>
    <?php
}, 99999);

// Also try body_class filter
add_filter('body_class', function($classes) {
    error_log('WSF: body_class filter running!');
    if (is_product_category()) {
        error_log('WSF: IS PRODUCT CATEGORY!');
    }
    return $classes;
});
