<?php
/**
 * AJAX Handler for Vinyl Profiles
 * Add this code to your theme's functions.php file
 */

// Register AJAX handlers for both logged-in and non-logged-in users
add_action('wp_ajax_ws_get_vinyl_profiles', 'ws_get_vinyl_profiles');
add_action('wp_ajax_nopriv_ws_get_vinyl_profiles', 'ws_get_vinyl_profiles');

/**
 * APPROACH 1: Database Query Method (Recommended)
 * Use this if you have profiles stored in database
 */
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

    // Build database query
    $table_name = $wpdb->prefix . 'vinyl_profiles'; // Change table name as per your database

    $query = "SELECT pdf_url FROM {$table_name} WHERE 1=1";
    $params = array();

    if ($cat_id) {
        $query .= " AND category_id = %d";
        $params[] = $cat_id;
    }

    if ($body_height) {
        $query .= " AND body_height = %s";
        $params[] = $body_height;
    }

    if ($picket_size) {
        $query .= " AND picket_size = %s";
        $params[] = $picket_size;
    }

    if ($rail_size) {
        $query .= " AND rail_size = %s";
        $params[] = $rail_size;
    }

    if ($panel_width) {
        $query .= " AND panel_width = %s";
        $params[] = $panel_width;
    }

    if ($lattice_top_rail_size) {
        $query .= " AND lattice_top_rail_size = %s";
        $params[] = $lattice_top_rail_size;
    }

    if ($gap) {
        $query .= " AND gap = %s";
        $params[] = $gap;
    }

    // Execute query
    if (!empty($params)) {
        $prepared_query = $wpdb->prepare($query, $params);
        $result = $wpdb->get_row($prepared_query);
    } else {
        $result = $wpdb->get_row($query);
    }

    // Return response
    if ($result && isset($result->pdf_url)) {
        wp_send_json_success([
            [
                'image_url' => $result->pdf_url,
                'message' => 'Profile found'
            ]
        ]);
    } else {
        wp_send_json_error([
            [
                'message' => 'No profile found matching your criteria. Please try different options.'
            ]
        ]);
    }
}


/**
 * APPROACH 2: Manual Array Method (Quick Temporary Solution)
 * Use this if database is not ready yet
 * Comment out APPROACH 1 above and uncomment this
 */
/*
function ws_get_vinyl_profiles() {
    // Get filter values from AJAX request
    $filters = isset($_POST['filters']) ? $_POST['filters'] : array();

    // Sanitize inputs
    $cat_id = isset($filters['cat_id']) ? intval($filters['cat_id']) : 0;
    $body_height = isset($filters['body_height']) ? sanitize_text_field($filters['body_height']) : '';
    $picket_size = isset($filters['picket_size']) ? sanitize_text_field($filters['picket_size']) : '';
    $rail_size = isset($filters['rail_size']) ? sanitize_text_field($filters['rail_size']) : '';
    $panel_width = isset($filters['panel_width']) ? sanitize_text_field($filters['panel_width']) : '';

    // Manual profile mappings - Add your PDF URLs here
    $profiles = array(
        // Vinyl Privacy (Cat ID: 18524)
        '18524_48_6_1-5-x-5-5_6' => site_url('/wp-content/uploads/profiles/privacy-48-6-1.5x5.5-6.pdf'),
        '18524_48_6_1-5-x-5-5_8' => site_url('/wp-content/uploads/profiles/privacy-48-6-1.5x5.5-8.pdf'),
        '18524_60_6_1-5-x-8_6' => site_url('/wp-content/uploads/profiles/privacy-60-6-1.5x8-6.pdf'),

        // Vinyl Pool Fence (Cat ID: 18527)
        '18527_48__6' => site_url('/wp-content/uploads/profiles/pool-48-6.pdf'),
        '18527_48__8' => site_url('/wp-content/uploads/profiles/pool-48-8.pdf'),
        '18527_60__6' => site_url('/wp-content/uploads/profiles/pool-60-6.pdf'),
        '18527_60__8' => site_url('/wp-content/uploads/profiles/pool-60-8.pdf'),

        // Vinyl Picket (Cat ID: 18526)
        '18526_3__6' => site_url('/wp-content/uploads/profiles/picket-3-6.pdf'),
        '18526_3__8' => site_url('/wp-content/uploads/profiles/picket-3-8.pdf'),
        '18526_42__6' => site_url('/wp-content/uploads/profiles/picket-42-6.pdf'),
        '18526_42__8' => site_url('/wp-content/uploads/profiles/picket-42-8.pdf'),
        '18526_48__6' => site_url('/wp-content/uploads/profiles/picket-48-6.pdf'),
        '18526_48__8' => site_url('/wp-content/uploads/profiles/picket-48-8.pdf'),

        // Add more mappings as needed...
    );

    // Create lookup key from filters
    $lookup_key = $cat_id . '_' . $body_height . '_' . $picket_size . '_' . $rail_size . '_' . $panel_width;

    // Find matching profile
    if (isset($profiles[$lookup_key])) {
        wp_send_json_success([
            [
                'image_url' => $profiles[$lookup_key],
                'message' => 'Profile found'
            ]
        ]);
    } else {
        wp_send_json_error([
            [
                'message' => 'No profile found matching your criteria: ' . $lookup_key
            ]
        ]);
    }
}
*/
