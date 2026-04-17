<?php
/**
 * AJAX Handler for Vinyl Profiles - DATABASE VERSION
 *
 * INSTALLATION STEPS:
 * 1. Run create-profiles-table.sql in phpMyAdmin to create the table
 * 2. Copy this entire code to your theme's functions.php file
 * 3. Upload PDF files to /wp-content/uploads/profiles/ folder
 * 4. Add data to wpg0_profiles table using the sample INSERT statements
 */

// Register AJAX handlers for both logged-in and non-logged-in users
add_action('wp_ajax_ws_get_vinyl_profiles', 'ws_get_vinyl_profiles');
add_action('wp_ajax_nopriv_ws_get_vinyl_profiles', 'ws_get_vinyl_profiles');

function ws_get_vinyl_profiles() {
    global $wpdb;

    // Get filter values from AJAX request
    $filters = isset($_POST['filters']) ? $_POST['filters'] : array();

    // Sanitize inputs
    $cat_id = isset($filters['cat_id']) ? intval($filters['cat_id']) : 0;
    $body_height = isset($filters['body_height']) ? sanitize_text_field($filters['body_height']) : '';
    $picket_size = isset($filters['picket_size']) ? sanitize_text_field($filters['picket_size']) : '';
    $rail_size = isset($filters['rail_size']) ? sanitize_text_field($filters['rail_size']) : '';
    $panel_width = isset($filters['panel_width']) ? sanitize_text_field($filters['panel_width']) : '';
    $lattice_top_rail_size = isset($filters['lattice_top_rail_size']) ? sanitize_text_field($filters['lattice_top_rail_size']) : '';
    $gap = isset($filters['gap']) ? sanitize_text_field($filters['gap']) : '';

    // Table name
    $table_name = $wpdb->prefix . 'profiles'; // Will be: wpg0_profiles

    // Build query
    $query = "SELECT pdf_url FROM {$table_name} WHERE 1=1";
    $params = array();

    // Add category condition
    if ($cat_id) {
        $query .= " AND category_id = %d";
        $params[] = $cat_id;
    }

    // Add filter conditions only if they are provided
    if ($body_height !== '') {
        $query .= " AND body_height = %s";
        $params[] = $body_height;
    }

    if ($picket_size !== '') {
        $query .= " AND picket_size = %s";
        $params[] = $picket_size;
    }

    if ($rail_size !== '') {
        $query .= " AND rail_size = %s";
        $params[] = $rail_size;
    }

    if ($panel_width !== '') {
        $query .= " AND panel_width = %s";
        $params[] = $panel_width;
    }

    if ($lattice_top_rail_size !== '') {
        $query .= " AND lattice_top_rail_size = %s";
        $params[] = $lattice_top_rail_size;
    }

    if ($gap !== '') {
        $query .= " AND gap = %s";
        $params[] = $gap;
    }

    // Limit to 1 result
    $query .= " LIMIT 1";

    // Execute query
    if (!empty($params)) {
        $prepared_query = $wpdb->prepare($query, $params);
        $result = $wpdb->get_row($prepared_query);
    } else {
        $result = $wpdb->get_row($query);
    }

    // Log for debugging (remove in production)
    error_log('Profile Query: ' . $prepared_query);
    error_log('Profile Result: ' . print_r($result, true));

    // Return response
    if ($result && isset($result->pdf_url)) {
        wp_send_json_success([
            [
                'image_url' => $result->pdf_url,
                'message' => 'Profile found successfully'
            ]
        ]);
    } else {
        wp_send_json_error([
            [
                'message' => 'No profile found matching your criteria. Please try different options or contact support.',
                'filters_used' => $filters,
                'query' => $prepared_query ?? $query
            ]
        ]);
    }
}
