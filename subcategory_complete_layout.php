<?php
/**
 * Complete Subcategory Layout Handler
 * Shows: Accordions + Form BEFORE Products
 *
 * Add this to your theme's functions.php:
 * require_once get_stylesheet_directory() . '/subcategory_complete_layout.php';
 */

if (!defined('ABSPATH')) exit;

add_action('wp_ajax_ws_get_vinyl_profiles', 'ws_get_vinyl_profiles');
add_action('wp_ajax_nopriv_ws_get_vinyl_profiles', 'ws_get_vinyl_profiles');

function ws_get_vinyl_profiles() {
    global $wpdb;

    $filters = isset($_POST['filters']) ? $_POST['filters'] : array();

    $cat_id = isset($filters['cat_id']) ? intval($filters['cat_id']) : 0;
    $body_height = isset($filters['body_height']) ? sanitize_text_field($filters['body_height']) : '';
    $picket_size = isset($filters['picket_size']) ? sanitize_text_field($filters['picket_size']) : '';
    $rail_size = isset($filters['rail_size']) ? sanitize_text_field($filters['rail_size']) : '';
    $panel_width = isset($filters['panel_width']) ? sanitize_text_field($filters['panel_width']) : '';
    $lattice_top_rail_size = isset($filters['lattice_top_rail_size']) ? sanitize_text_field($filters['lattice_top_rail_size']) : '';
    $gap = isset($filters['gap']) ? sanitize_text_field($filters['gap']) : '';

    $table_name = $wpdb->prefix . 'profiles';

    $query = "SELECT pdf_url FROM {$table_name} WHERE 1=1";
    $params = array();

    if ($cat_id) {
        $query .= " AND category_id = %d";
        $params[] = $cat_id;
    }

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

    $query .= " LIMIT 1";

    if (!empty($params)) {
        $prepared_query = $wpdb->prepare($query, $params);
        $result = $wpdb->get_row($prepared_query);
    } else {
        $result = $wpdb->get_row($query);
    }

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
                'message' => 'No profile found matching your criteria. Please try different options.',
                'filters_used' => $filters
            ]
        ]);
    }
}

add_action('wp_enqueue_scripts', 'wsf_force_load_wooaddon_scripts');
function wsf_force_load_wooaddon_scripts() {
    if (is_product_category() || is_singular('product')) {
        wp_enqueue_style('wooaddon-select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css');
        wp_enqueue_style('wooaddon-sweetalert', WP_PLUGIN_URL . '/wooaddon/css/sweetalert.css');
        wp_enqueue_style('wooaddon-style', WP_PLUGIN_URL . '/wooaddon/css/styles.css');

        wp_enqueue_script('wooaddon-select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js', array('jquery'), null, true);
        wp_enqueue_script('wooaddon-dropzone', WP_PLUGIN_URL . '/wooaddon/js/dropzone.js', array('jquery'), null, true);
        wp_enqueue_script('wooaddon-validate', WP_PLUGIN_URL . '/wooaddon/js/jquery.validate.js', array('jquery'), null, true);
        wp_enqueue_script('wooaddon-additional', WP_PLUGIN_URL . '/wooaddon/js/additional-methods.min.js', array('jquery'), null, true);
        wp_enqueue_script('wooaddon-sweetalert', WP_PLUGIN_URL . '/wooaddon/js/sweetalert.min.js', array('jquery'), null, true);
        wp_enqueue_script('wooaddon-calling-dropzone', WP_PLUGIN_URL . '/wooaddon/js/calling_dropzone.js', array('jquery', 'wooaddon-validate'), null, true);

        // Pass ajaxurl to wooaddon script
        wp_localize_script('wooaddon-calling-dropzone', 'wooaddon', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('wooaddon_nonce'),
        ));
    }
}

add_action('wp_footer', 'wsf_show_accordions_before_products', 20);

function wsf_show_accordions_before_products() {
    $is_category_page = is_product_category();
    $is_product_page = is_singular('product');

    if (!$is_category_page && !$is_product_page) {
        return;
    }

    static $already_rendered = false;
    if ($already_rendered) return;
    $already_rendered = true;

    if ($is_category_page) {
        $current_cat = get_queried_object();
        if (!$current_cat || !isset($current_cat->term_id)) return;

        if (empty($current_cat->parent) || $current_cat->parent == 0) return;

        $cat_id = $current_cat->term_id;
        $parent_id = $current_cat->parent;

        while ($parent_id != 0) {
            $parent_cat = get_term($parent_id, 'product_cat');
            if (!$parent_cat || is_wp_error($parent_cat)) break;

            if ($parent_cat->parent == 0) break;

            $cat_id = $parent_id;
            $parent_id = $parent_cat->parent;
        }
    }

    if ($is_product_page) {
        global $post;
        $product_cats = get_the_terms($post->ID, 'product_cat');

        if (!$product_cats || is_wp_error($product_cats)) return;

        $current_cat = $product_cats[0];
        $cat_id = $current_cat->term_id;
        $parent_id = $current_cat->parent;

        while ($parent_id != 0) {
            $parent_cat = get_term($parent_id, 'product_cat');
            if (!$parent_cat || is_wp_error($parent_cat)) break;

            if ($parent_cat->parent == 0) break;

            $cat_id = $parent_id;
            $parent_id = $parent_cat->parent;
        }
    }

    $first_level_cat = get_term($cat_id, 'product_cat');
    $intro_text      = $first_level_cat ? $first_level_cat->description : '';
    $video_ids       = get_term_meta($cat_id, 'category_video_ids', true);
    $review_link     = get_term_meta($cat_id, 'category_review_link', true);
    $specifications  = get_term_meta($cat_id, 'category_specifications', true);
    $pdf_downloads   = get_term_meta($cat_id, 'category_pdf_downloads', true);
    $category_images = get_term_meta($cat_id, 'category_images', true);
    $profile_type    = get_term_meta($cat_id, 'category_profile_type', true);

    $videos = array();
    if (!empty($video_ids)) {
        $videos = array_filter(array_map('trim', explode(',', $video_ids)));
    }

    // Parse PDF downloads (format: Title|URL, one per line)
    $pdfs = array();
    if (!empty($pdf_downloads)) {
        $lines = explode("\n", $pdf_downloads);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            $parts = explode('|', $line, 2);
            if (count($parts) == 2) {
                $pdfs[] = array(
                    'title' => trim($parts[0]),
                    'url' => trim($parts[1])
                );
            }
        }
    }

    ob_start();
    ?>
    <div class="wsf-category-accordion-section" style="width: 100%; max-width: 1200px; margin-top: -70px; padding: 20px; clear: both;">

        <div class="wsf-accordions" style="margin-bottom: 30px;">

            <div class="wsf-acc-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden; background: #fff;">
                <div class="wsf-acc-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wsfToggleAcc('overview')">
                    <span>Overview</span>
                    <span class="wsf-acc-icon" style="display: inline-flex; align-items: center; transition: transform 0.3s ease;">
                        <svg width="30" height="30" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 7.5L10 12.5L15 7.5" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
                <div class="wsf-acc-content" id="wsf-acc-overview" style="display: none; padding: 20px; border-top: 1px solid #e0e0e0;">
                    <?php if (!empty($intro_text)): ?>
                        <?php echo wpautop(wp_kses_post($intro_text)); ?>
                    <?php else: ?>
                        <p style="color: #888;">No overview content available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="wsf-acc-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden; background: #fff;">
                <div class="wsf-acc-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wsfToggleAcc('specifications')">
                    <span>Specifications</span>
                    <span class="wsf-acc-icon" style="display: inline-flex; align-items: center; transition: transform 0.3s ease;">
                        <svg width="30" height="30" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 7.5L10 12.5L15 7.5" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
                <div class="wsf-acc-content" id="wsf-acc-specifications" style="display: none; padding: 20px; border-top: 1px solid #e0e0e0;">
                    <?php if (!empty($specifications)): ?>
                        <?php
                        // Parse specifications with support for text, images, and PDFs
                        $spec_lines = explode("\n", $specifications);
                        foreach ($spec_lines as $line) {
                            $line = trim($line);
                            if (empty($line)) continue;

                            // Check if line contains pipe character (potential IMAGE or PDF)
                            if (strpos($line, '|') !== false) {
                                // Split by pipe and check first part
                                $parts = array_map('trim', explode('|', $line));

                                // Check if it's an IMAGE line
                                if (strcasecmp($parts[0], 'IMAGE') === 0 && isset($parts[1])) {
                                    $url = $parts[1];
                                    if (!empty($url)) {
                                        echo '<div style="margin: 15px 0;">';
                                        echo '<img src="' . esc_url($url) . '" alt="Specification Image" style="max-width: 100%; height: auto; padding: 5px; display: block;">';
                                        echo '</div>';
                                    }
                                    continue;
                                }

                                // Check if it's a PDF line
                                if (strcasecmp($parts[0], 'PDF') === 0 && isset($parts[1])) {
                                    $pdf_url = $parts[1];
                                    $link_text = isset($parts[2]) ? $parts[2] : 'Download PDF';
                                    $proxy_url = get_stylesheet_directory_uri() . '/download-pdf.php?url=' . urlencode($pdf_url) . '&name=' . urlencode($link_text);
                                    echo '<div style="display: flex; align-items: center; margin: 10px 0;">';
                                    echo '<a href="' . esc_url($proxy_url) . '" class="wsf-pdf-link" style="display: inline-flex; align-items: center; text-decoration: none; color: #327A1F; font-size: 16px; font-weight: 700; transition: color 0.2s ease;">';
                                    echo '<i class="fa-solid fa-file-arrow-down" style="font-size: 25px; color: #327a1f; margin-right: 10px;"></i>';
                                    echo '<span>' . esc_html($link_text) . '</span>';
                                    echo '</a>';
                                    echo '</div>';
                                    continue;
                                }
                            }

                            echo '<p style="margin: 8px 0; line-height: 1.6; color: #333;">' . wp_kses_post($line) . '</p>';
                        }
                        ?>
                    <?php else: ?>
                        <p style="color: #888;">No specifications available at this time.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="wsf-acc-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden; background: #fff;">
                <div class="wsf-acc-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wsfToggleAcc('videos')">
                    <span>Videos</span>
                    <span class="wsf-acc-icon" style="display: inline-flex; align-items: center; transition: transform 0.3s ease;">
                        <svg width="30" height="30" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 7.5L10 12.5L15 7.5" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
                <div class="wsf-acc-content" id="wsf-acc-videos" style="display: none; padding: 20px; border-top: 1px solid #e0e0e0;">
                    <?php if (!empty($videos)): ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                            <?php
                            $video_index = 0;
                            foreach ($videos as $video_id):
                                $clean_id = trim($video_id);
                                $video_index++;
                            ?>
                                <div class="wsf-video-container" data-video-id="<?php echo esc_attr($clean_id); ?>" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background: #000; cursor: pointer;">
                                    <img
                                        src="https://img.youtube.com/vi/<?php echo esc_attr($clean_id); ?>/maxresdefault.jpg"
                                        alt="Video thumbnail"
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
                                        onerror="this.src='https://img.youtube.com/vi/<?php echo esc_attr($clean_id); ?>/hqdefault.jpg';"
                                    >
                                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 68px; height: 48px; background: rgba(255,0,0,0.8); border-radius: 12px; pointer-events: none;">
                                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-35%, -50%); width: 0; height: 0; border-left: 18px solid white; border-top: 12px solid transparent; border-bottom: 12px solid transparent;"></div>
                                    </div>
                                    <div class="wsf-iframe-wrapper" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="color: #888;">No videos available for this category.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="wsf-acc-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden; background: #fff;">
                <div class="wsf-acc-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wsfToggleAcc('reviews')">
                    <span>Reviews</span>
                    <span class="wsf-acc-icon" style="display: inline-flex; align-items: center; transition: transform 0.3s ease;">
                        <svg width="30" height="30" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 7.5L10 12.5L15 7.5" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
                <div class="wsf-acc-content" id="wsf-acc-reviews" style="display: none; padding: 20px; border-top: 1px solid #e0e0e0;">
                    <?php if (!empty($review_link)): ?>
                        <p style="margin-bottom: 15px;">See what our customers are saying.</p>
                        <a href="<?php echo esc_url($review_link); ?>" target="_blank" rel="noopener" style="display: inline-block; background: #4285f4; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600;">View Google Reviews</a>
                    <?php else: ?>
                        <p style="color: #888;">No review link configured.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="wsf-acc-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden; background: #fff;">
                <div class="wsf-acc-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wsfToggleAcc('downloads')">
                    <span>Downloads</span>
                    <span class="wsf-acc-icon" style="display: inline-flex; align-items: center; transition: transform 0.3s ease;">
                        <svg width="30" height="30" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 7.5L10 12.5L15 7.5" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
                <div class="wsf-acc-content" id="wsf-acc-downloads" style="display: none; padding: 20px; border-top: 1px solid #e0e0e0;">
                    <?php if (!empty($pdfs)): ?>
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <?php foreach ($pdfs as $pdf):
                                $proxy_url = get_stylesheet_directory_uri() . '/download-pdf.php?url=' . urlencode($pdf['url']) . '&name=' . urlencode($pdf['title']);
                            ?>
                                <div style="display: flex; align-items: center;">
                                    <a href="<?php echo esc_url($proxy_url); ?>" class="wsf-pdf-link" style="display: inline-flex; align-items: center; text-decoration: none; color: #327A1F; font-size: 16px; font-weight: 700; transition: color 0.2s ease;">
                                        <i class="fa-solid fa-file-arrow-down" style="font-size: 25px; color: #327a1f; margin-right: 10px;"></i>
                                        <span><?php echo esc_html($pdf['title']); ?></span>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="color: #888;">No downloads available at this time.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <div class="wsf-contact-form-wrapper" style="margin: 30px 0; padding: 20px; border-radius: 8px;">
            <h2 style="text-align: center; font-size: 28px; font-weight: 700; color: #333; margin-bottom: 15px;">Get Your Free Vinyl Fencing Quote</h2>
            <p style="text-align: center; font-size: 16px; color: #666; margin-bottom: 20px;">
                Please Fill Out This Form For A Free Quote. You Can Email Customer Service Directly at:<br>
                <a href="mailto:orders@wholesalefencing.com" style="color: #327A1F; text-decoration: none; font-weight: 600;">orders@wholesalefencing.com</a>
            </p>
            <div class="contact-quick">
                <?php echo do_shortcode('[wooaddon_from]'); ?>
            </div>
        </div>

        <?php if (!empty($profile_type)): ?>
        <div class="wsf-acc-item" style="margin-bottom: 30px; border-radius: 6px; overflow: hidden; background: #fff;">
            <div class="wsf-acc-header" style="background: #f5f5f5; padding: 15px; cursor: pointer; display: flex; justify-content: center; align-items: center; font-size: 26px; font-weight: 700; color: #333; position: relative;" onclick="wsfToggleAcc('profiles')">
                <span>Choose A Profile</span>
                <span class="wsf-acc-icon" style="display: inline-flex; align-items: center; transition: transform 0.3s ease; position: absolute; right: 20px;">
                    <svg width="30" height="30" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 7.5L10 12.5L15 7.5" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>
            <div class="wsf-acc-content" id="wsf-acc-profiles" style="display: block; padding: 20px; background: #fff; border-left: 1px solid #e0e0e0; border-right: 1px solid #e0e0e0; border-bottom: 1px solid #e0e0e0;">
                <p style="text-align: center; font-size: 25px; font-weight: 700; color: #333; margin-bottom: 20px;">Most Popular option is highlighted.</p>

                <?php
                $profile_file = get_stylesheet_directory() . '/profile-templates/' . $profile_type . '.php';

                if (file_exists($profile_file)) {
                    include($profile_file);
                } else {
                    echo '<p style="color: #888; text-align: center;">Profile template not found.</p>';
                }
                ?>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <style>
    .wsf-accordions .wsf-acc-header:hover { background: #ebebeb !important; }
    .wsf-accordions .wsf-acc-header:hover .wsf-acc-icon { color: #327A1F !important; }
    .wsf-pdf-link:hover { color: #000 !important; }

    .profile-filters-container {
        max-width: 100%;
        margin: 20px auto;
        padding: 20px;
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
    }
    .profile-section {
        width: 100%;
    }

    .profile-header {
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e0e0e0;
        text-align: center;
    }
    .profile-title {
        font-size: 20px;
        font-weight: 700;
        color: #333;
        margin: 0;
    }

    .profile-popular-text {
        text-align: center;
        font-size: 14px;
        color: #666;
        margin-bottom: 20px;
    }

    .filters-horizontal-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 30px;
    }
    @media (max-width: 768px) {
        .filters-horizontal-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 480px) {
        .filters-horizontal-grid {
            grid-template-columns: 1fr;
        }
    }

    .filter-column {
        display: flex;
        flex-direction: column;
    }
    .filter-column-header {
        text-align: center;
        margin-bottom: 10px;
    }
    .filter-column-header img {
        display: block;
        margin: 0 auto 10px;
        max-width: 80px;
        height: auto;
    }

    .profile-accordion-header {
        width: 100%;
        background: transparent;
        color: #333;
        padding: 12px 15px;
        font-weight: 600;
        font-size: 13px;
        letter-spacing: 0.5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 4px;
        border: 1px solid #ddd;
        cursor: pointer;
        transition: all 0.2s;
    }
    .profile-accordion-header:hover {
        background: #f5f5f5;
    }
    .profile-accordion-header.open {
        background: #4a7c32 !important;
        color: white;
        border-color: #4a7c32;
    }
    .profile-accordion-header.error-highlight {
        border: 2px solid #dc3545 !important;
        animation: shake 0.3s;
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .accordion-check-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        color: #333;
        transition: all 0.3s ease;
    }
    .accordion-check-icon svg {
        width: 22px;
        height: 22px;
    }
    .accordion-check-icon svg circle {
        fill: currentColor;
        stroke: currentColor;
    }
    .accordion-check-icon svg path {
        stroke: white;
    }
    .profile-accordion-header.open .accordion-check-icon {
        color: white;
        transform: rotate(180deg);
    }
    .profile-accordion-header.open .accordion-check-icon svg path {
        stroke: #4a7c32;
    }

    .profile-accordion-content {
        display: none;
        flex-direction: column;
        gap: 5px;
        margin-top: 10px;
    }
    .profile-accordion-content.show {
        display: flex;
    }
    .option-box {
        padding: 8px 12px;
        border: 1px solid #ddd;
        background: white;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        border-radius: 4px;
        font-size: 13px;
    }
    .option-box:hover {
        background: #f5f5f5;
        border-color: #4a7c32;
    }
    .option-box.popular {
        background: #ffffcc !important;
        border-color: #f0c000;
        font-weight: 600;
    }
    .option-box.selected {
        background: #4a7c32 !important;
        color: white !important;
        border-color: #4a7c32;
        font-weight: 600;
    }

    .profile-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
    }
    .profile-btn {
        padding: 8px 15px;
        background: #4a7c32;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: background 0.3s;
        text-transform: uppercase;
    }
    .profile-btn:hover {
        background: #3a6025;
    }
    .loader-box {
        position: fixed;
        top: 0;
        z-index: 9999;
        width: 100%;
        height: 100%;
        background: #ffffff78;
        display: none;
    }
    .loader-filter {
        border: 6px solid #f3f3f3;
        border-radius: 50%;
        border-top: 6px solid #3498db;
        width: 40px;
        height: 40px;
        animation: spin 2s linear infinite;
        transform: translate(-50%, -50%);
        position: absolute;
        left: 50%;
        top: 50%;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); border-top-color: #a80532; }
        100% { transform: rotate(360deg); border-top-color: #2e3192; }
    }
    .pdf-error {
        display: flex;
        align-items: center;
        color: #dc3545;
        padding: 15px;
    }
    .error-icon {
        padding-right: 10px;
        font-size: 21px;
    }
    </style>
    <?php

    $accordion_html = ob_get_clean();
    ?>
    <script>
    window.wsfToggleAcc = function(section) {
        var content = document.getElementById('wsf-acc-' + section);
        if (!content) return;

        var header = content.previousElementSibling;
        var icon = header.querySelector('.wsf-acc-icon');
        var svg = icon.querySelector('svg path');
        var isOpen = content.style.display === 'block';

        document.querySelectorAll('.wsf-accordions .wsf-acc-content').forEach(function(item) {
            if (item !== content) item.style.display = 'none';
        });

        document.querySelectorAll('.wsf-accordions .wsf-acc-icon').forEach(function(ic) {
            if (ic !== icon) {
                ic.style.transform = 'rotate(0deg)';
                var otherSvg = ic.querySelector('svg path');
                if (otherSvg) otherSvg.setAttribute('stroke', '#666');
            }
        });

        if (isOpen) {
            content.style.display = 'none';
            icon.style.transform = 'rotate(0deg)';
            if (svg) svg.setAttribute('stroke', '#666');
        } else {
            content.style.display = 'block';
            icon.style.transform = 'rotate(180deg)';
            if (svg) svg.setAttribute('stroke', '#327A1F');
        }
    };

    window.wsfLoadVideo = function(container) {
        var videoId = container.getAttribute('data-video-id');
        if (!videoId) return;

        var iframeWrapper = container.querySelector('.wsf-iframe-wrapper');
        if (!iframeWrapper) return;

        var iframe = document.createElement('iframe');
        iframe.style.cssText = 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;';
        iframe.src = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0&modestbranding=1';
        iframe.title = 'YouTube video player';
        iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
        iframe.setAttribute('allowfullscreen', '');

        iframeWrapper.appendChild(iframe);
        iframeWrapper.style.display = 'block';

        container.querySelector('img').style.display = 'none';
        container.querySelector('div[style*="border-left"]').parentElement.style.display = 'none';
    };

    (function() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', insertAccordions);
        } else {
            insertAccordions();
        }

        function insertAccordions() {
            var wsfCatPage = document.querySelector('.wsf-cat-page');
            if (wsfCatPage) {
                var productCatInside = wsfCatPage.querySelector('#wsf-product-categories');
                if (productCatInside) {
                    productCatInside.insertAdjacentHTML('beforebegin', <?php echo json_encode($accordion_html); ?>);
                } else {
                    wsfCatPage.insertAdjacentHTML('beforeend', <?php echo json_encode($accordion_html); ?>);
                }
                attachVideoHandlers();
                return;
            }

            var hasSubcategories = false;

            if (document.getElementById('wsf-sub-categories')) {
                hasSubcategories = true;
            }

            if (!hasSubcategories && document.getElementById('wsf-product-categories')) {
                hasSubcategories = true;
            }

            if (!hasSubcategories) {
                var allHeadings = document.querySelectorAll('h1, h2, h3, h4, .section-title');
                for (var i = 0; i < allHeadings.length; i++) {
                    var text = allHeadings[i].textContent.toLowerCase();
                    if (text.includes('sub categor') || text.includes('product categor')) {
                        hasSubcategories = true;
                        break;
                    }
                }
            }

            if (!hasSubcategories) {
                var cardGrid = document.querySelector('.dpc-card-grid');
                if (cardGrid && cardGrid.querySelector('.dpc-cat-card')) {
                    hasSubcategories = true;
                }
            }

            if (!hasSubcategories) {
                var relatedProductsHeadings = document.querySelectorAll('h2, h3, h4');
                for (var i = 0; i < relatedProductsHeadings.length; i++) {
                    var text = relatedProductsHeadings[i].textContent.toLowerCase();
                    if (text.includes('related product') || text.includes('you may also like')) {
                        hasSubcategories = true;
                        break;
                    }
                }
            }

            if (!hasSubcategories) return;

            var productCatSection = document.getElementById('wsf-product-categories');
            if (productCatSection) {
                productCatSection.insertAdjacentHTML('beforebegin', <?php echo json_encode($accordion_html); ?>);
                attachVideoHandlers();
                return;
            }

            var isProductPage = document.body.classList.contains('single-product');
            if (isProductPage) {
                var termGroups = document.querySelectorAll('.wsf-term-group');
                if (termGroups.length > 0) {
                    termGroups[0].insertAdjacentHTML('afterend', <?php echo json_encode($accordion_html); ?>);
                    attachVideoHandlers();
                    return;
                }

                var categoryGrid = document.querySelector('.dpc-card-grid');
                if (categoryGrid && categoryGrid.querySelector('.dpc-cat-card')) {
                    var gridParent = categoryGrid.parentElement;
                    while (gridParent && !gridParent.classList.contains('section')) {
                        gridParent = gridParent.parentElement;
                    }
                    if (gridParent) {
                        gridParent.insertAdjacentHTML('afterend', <?php echo json_encode($accordion_html); ?>);
                        attachVideoHandlers();
                        return;
                    }
                }

                var relatedSection = null;
                var allSections = document.querySelectorAll('.related.products, section');
                for (var i = 0; i < allSections.length; i++) {
                    var heading = allSections[i].querySelector('h2, h3');
                    if (heading) {
                        var headingText = heading.textContent.toLowerCase();
                        if (headingText.includes('related product') || headingText.includes('you may also like')) {
                            relatedSection = allSections[i];
                            break;
                        }
                    }
                }
                if (relatedSection) {
                    relatedSection.insertAdjacentHTML('afterend', <?php echo json_encode($accordion_html); ?>);
                    attachVideoHandlers();
                    return;
                }

                var productRows = document.querySelectorAll('.row.row-small');
                if (productRows.length > 0) {
                    productRows[productRows.length - 1].insertAdjacentHTML('afterend', <?php echo json_encode($accordion_html); ?>);
                    attachVideoHandlers();
                    return;
                }
            }

            var allRows = document.querySelectorAll('.row');
            var subCatRow = null;
            for (var i = 0; i < allRows.length; i++) {
                var headings = allRows[i].querySelectorAll('h2, h3, .section-title');
                for (var j = 0; j < headings.length; j++) {
                    var text = headings[j].textContent.trim().toLowerCase();
                    if (text.includes('sub categories') || text.includes('subcategories')) {
                        subCatRow = allRows[i];
                        break;
                    }
                }
                if (subCatRow) break;
            }

            if (subCatRow) {
                subCatRow.insertAdjacentHTML('afterend', <?php echo json_encode($accordion_html); ?>);
                attachVideoHandlers();
                return;
            }

            var productGrid = document.querySelector('.product-category');
            if (!productGrid) productGrid = document.querySelector('.products.row');

            if (productGrid) {
                var hasSubcats = productGrid.querySelector('.product-small.col') !== null;
                if (hasSubcats) {
                    productGrid.insertAdjacentHTML('afterend', <?php echo json_encode($accordion_html); ?>);
                    attachVideoHandlers();
                    return;
                }
            }

            var productCatHeading = null;
            var allHeadings = document.querySelectorAll('h2, h3, .section-title');
            for (var k = 0; k < allHeadings.length; k++) {
                if (allHeadings[k].textContent.trim().toLowerCase().includes('product category')) {
                    productCatHeading = allHeadings[k];
                    break;
                }
            }

            if (productCatHeading) {
                var parent = productCatHeading.parentElement;
                while (parent && !parent.classList.contains('row')) {
                    parent = parent.parentElement;
                }
                if (parent) {
                    parent.insertAdjacentHTML('beforebegin', <?php echo json_encode($accordion_html); ?>);
                    attachVideoHandlers();
                    return;
                }
            }

            var shopContainer = document.querySelector('.shop-container');
            if (shopContainer) {
                shopContainer.insertAdjacentHTML('beforeend', <?php echo json_encode($accordion_html); ?>);
            } else {
                document.querySelector('main').insertAdjacentHTML('beforeend', <?php echo json_encode($accordion_html); ?>);
            }
            attachVideoHandlers();
        }

        function attachVideoHandlers() {
            var videoContainers = document.querySelectorAll('.wsf-video-container');
            videoContainers.forEach(function(container) {
                container.addEventListener('click', function() {
                    window.wsfLoadVideo(container);
                });
            });

            makeSubcategoryCardsToggleable();

            setTimeout(function() {
                var profilesHeader = document.querySelector('#wsf-acc-profiles');
                if (profilesHeader && profilesHeader.previousElementSibling) {
                    var icon = profilesHeader.previousElementSibling.querySelector('.wsf-acc-icon');
                    if (icon) {
                        icon.style.transform = 'rotate(180deg)';
                        var svg = icon.querySelector('svg path');
                        if (svg) svg.setAttribute('stroke', '#4CAF50');
                    }
                }
            }, 100);
        }

        function makeSubcategoryCardsToggleable() {
            var subCatSection = document.getElementById('wsf-sub-categories');

            if (!subCatSection) {
                var termGroups = document.querySelectorAll('.wsf-term-group');
                if (termGroups.length > 0) {
                    subCatSection = termGroups[0];
                    for (var i = 1; i < termGroups.length; i++) {
                        var h = termGroups[i].querySelector('h2, h3, h1');
                        if (h && !h.hasAttribute('data-wsf-centered')) {
                            h.style.textAlign = 'center';
                            h.setAttribute('data-wsf-centered', 'true');
                        }
                    }
                } else {
                    return;
                }
            }

            var heading = subCatSection.querySelector('h2, h3, h1');
            if (!heading) return;

            if (heading.hasAttribute('data-wsf-initialized')) return;

            var cardsContainer = null;
            var nextEl = subCatSection.nextElementSibling;

            if (nextEl && (nextEl.classList.contains('dpc-card-grid') || nextEl.classList.contains('category-grid'))) {
                cardsContainer = nextEl;
            } else {
                cardsContainer = subCatSection.querySelector('.dpc-card-grid, .category-grid, [class*="grid"]');
            }

            if (!cardsContainer) return;

            var headingText = heading.textContent;

            heading.style.cursor = 'pointer';
            heading.style.display = 'flex';
            heading.style.justifyContent = 'center';
            heading.style.alignItems = 'center';
            heading.style.padding = '15px 20px';
            heading.style.background = '#f5f5f5';
            heading.style.borderRadius = '6px';
            heading.style.marginBottom = '20px';
            heading.style.position = 'relative';

            var iconHTML = '<span class="wsf-subcat-cards-toggle-icon" style="position: absolute; right: 20px; display: inline-flex; align-items: center; transition: transform 0.3s ease;"><svg width="30" height="30" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 7.5L10 12.5L15 7.5" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>';
            heading.innerHTML = '<span style="flex: 1; text-align: center;">' + headingText + '</span>' + iconHTML;

            cardsContainer.style.transition = 'max-height 0.4s ease, opacity 0.3s ease';
            cardsContainer.style.overflow = 'hidden';
            cardsContainer.style.maxHeight = '0';
            cardsContainer.style.opacity = '0';
            cardsContainer.style.display = 'grid';
            cardsContainer.style.pointerEvents = 'none';

            var isOpen = false;

            heading.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var icon = heading.querySelector('.wsf-subcat-cards-toggle-icon');
                var accordionSection = document.querySelector('.wsf-category-accordion-section');

                if (!isOpen) {
                    setTimeout(function() {
                        cardsContainer.style.maxHeight = '3000px';
                        cardsContainer.style.opacity = '1';
                        cardsContainer.style.pointerEvents = 'auto';
                    }, 10);
                    icon.style.transform = 'rotate(180deg)';
                    if (accordionSection) accordionSection.style.marginTop = '0px';
                    isOpen = true;
                } else {
                    cardsContainer.style.maxHeight = '0';
                    cardsContainer.style.opacity = '0';
                    cardsContainer.style.pointerEvents = 'none';
                    icon.style.transform = 'rotate(0deg)';
                    if (accordionSection) accordionSection.style.marginTop = '-70px';
                    isOpen = false;
                }
            });

            heading.setAttribute('data-wsf-initialized', 'true');
        }

        setTimeout(function() {
            makeSubcategoryCardsToggleable();
        }, 500);

        function checkAccordionVisibility() {
            var accordionSection = document.querySelector('.wsf-category-accordion-section');
            if (!accordionSection) return;

            var hasSubcategories = false;

            if (document.getElementById('wsf-sub-categories') || document.getElementById('wsf-product-categories')) {
                hasSubcategories = true;
            }

            if (!hasSubcategories) {
                var allHeadings = document.querySelectorAll('h1, h2, h3, h4');
                for (var i = 0; i < allHeadings.length; i++) {
                    var text = allHeadings[i].textContent.toLowerCase();
                    if (text.includes('sub categor') || text.includes('product categor')) {
                        hasSubcategories = true;
                        break;
                    }
                }
            }

            if (!hasSubcategories) {
                var cardGrid = document.querySelector('.dpc-card-grid');
                if (cardGrid && cardGrid.querySelector('.dpc-cat-card')) {
                    hasSubcategories = true;
                }
            }

            accordionSection.style.display = hasSubcategories ? 'block' : 'none';
        }

        var lastUrl = window.location.href;
        setInterval(function() {
            if (window.location.href !== lastUrl) {
                lastUrl = window.location.href;
                setTimeout(checkAccordionVisibility, 500);
            }
        }, 500);
    })();

    window.submitUserForm = function() {
        var form = document.getElementById('primaryPostForm');
        if (form) {
            jQuery('#primaryPostForm').submit();
        }
    };

    jQuery(document).ready(function($) {
        $(document).on('click', '.profile-accordion-header', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).toggleClass('open');
            $(this).next('.profile-accordion-content').toggleClass('show');
        });

        $(document).on('click', '.option-box', function() {
            var container = $(this).closest('.profile-accordion-content');
            container.find('.option-box').removeClass('selected');
            $(this).addClass('selected');

            container.prev('.profile-accordion-header').removeClass('error-highlight');

            if ($(this).hasClass('sp-gap-option')) {
                var gapValue = $(this).data('value');
                var bodyContent = $('.sp-body-content');

                var bodyOptions = {
                    '1-2': ['35-1-2', '39-1-2', '43-1-2', '47-1-2', '51-1-2', '55-1-2', '59-1-2', '63-1-2', '67-1-2', '71-1-2', '75-1-2', '79-1-2', '83-1-2', '87-1-2', '91-1-2', '95-1-2', '99-1-2', '103-1-2', '107-1-2', '111-1-2', '115-1-2', '119-1-2'],
                    '1': ['35', '39-1-2', '44', '48-1-2', '53', '57-1-2', '62', '66-1-2', '71', '75-1-2', '80-1-2', '84-1-2', '89', '93-1-2', '98', '102-1-2', '107', '111-1-2', '116'],
                    '1-3-8': ['37-5-8', '42-1-2', '47-3-8', '52-1-4', '57-1-8', '62', '66-7-8', '71-3-4', '76-5-8', '81-1-2', '86-3-8', '91-1-4', '96-1-8', '101', '105-7-8', '110-3-4', '115-5-8']
                };

                var options = bodyOptions[gapValue] || [];
                bodyContent.empty();

                if (options.length > 0) {
                    options.forEach(function(val) {
                        var parts = val.split('-');
                        var displayVal = parts[0];
                        if (parts.length > 1) {
                            displayVal += ' ' + parts.slice(1).join('/');
                        }
                        bodyContent.append('<div class="option-box" data-value="' + val + '">' + displayVal + '</div>');
                    });
                }
            }

            if ($(this).hasClass('pr-width-option')) {
                var widthValue = $(this).data('value');
                var bodyContent = $('.pr-body-content');

                var bodyOptions = ['35', '38-1-2', '42', '45-1-2', '49', '52-1-2', '56', '59-1-2', '63', '66-1-2', '70', '73-1-2', '77', '80-1-2', '84', '87-1-2', '91', '94-1-2', '98', '101-1-2', '105'];

                bodyContent.empty();
                bodyOptions.forEach(function(val) {
                    var parts = val.split('-');
                    var displayVal = parts[0];
                    if (parts.length > 1) {
                        displayVal += ' ' + parts.slice(1).join('/');
                    }
                    bodyContent.append('<div class="option-box" data-value="' + val + '">' + displayVal + '</div>');
                });
            }

            if ($(this).hasClass('mix-rail-option')) {
                var railValue = $(this).data('value');
                var bodyContent = $('.mix-body-content');

                var bodyOptions = {
                    '3-rail': ['37', '40-1-2', '44', '47-1-2', '51', '54-1-2', '58', '61-1-2', '65', '68-1-2', '72', '75-1-2', '79', '82-1-2', '86', '89-1-2', '93', '96-1-2', '100', '103-1-2', '107'],
                    '4-rail': ['34-1-2', '38', '41-1-2', '45', '48-1-2', '52', '55-1-2', '59', '62-1-2', '66', '69-1-2', '73', '76-1-2', '80', '83-1-2', '87', '90-1-2', '94', '97-1-2', '101', '104-1-2', '108']
                };

                var options = bodyOptions[railValue] || [];
                bodyContent.empty();

                if (options.length > 0) {
                    options.forEach(function(val) {
                        var parts = val.split('-');
                        var displayVal = parts[0];
                        if (parts.length > 1) {
                            displayVal += ' ' + parts.slice(1).join('/');
                        }
                        bodyContent.append('<div class="option-box" data-value="' + val + '">' + displayVal + '</div>');
                    });
                }
            }
        });

        $(document).on('click', '.view-image, .profile-btn', function() {
            var cat_id = $(this).data('cat-id');
            var btn_name = $(this).data('btn-name');
            var pdfUrl = $(this).data('pdf-url');
            var filterGroup = $(this).data('filter-group');

            if (pdfUrl) {
                if (btn_name == 'vi') {
                    window.open(pdfUrl, '_blank');
                } else if (btn_name == 'dwn') {
                    downloadPDF(pdfUrl);
                }
                return;
            }

            var selectedFilters = {};
            var allSelected = true;
            var missingFilters = [];

            $('.profile-accordion-header').removeClass('error-highlight');

            $('.profile-accordion-header[data-group="' + filterGroup + '"]').each(function() {
                var filterKey = $(this).data('filter-key');
                var selectedValue = $(this).next('.profile-accordion-content').find('.option-box.selected').data('value');

                if (filterKey && selectedValue !== undefined) {
                    selectedFilters[filterKey] = selectedValue;
                } else if (filterKey) {
                    allSelected = false;
                    $(this).addClass('error-highlight');
                    missingFilters.push($(this).find('span').first().text());
                }
            });

            if (!allSelected) {
                return;
            }

            selectedFilters['cat_id'] = cat_id;

            $('.loader-box').show();

            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    filters: selectedFilters,
                    action: 'ws_get_vinyl_profiles'
                },
                success: function(response) {
                    if (response.success && response.data[0] && response.data[0].image_url) {
                        handleAjaxResponse(response, cat_id, btn_name);
                    } else {
                        alert('Error: ' + (response.data && response.data[0] ? response.data[0].message : 'No profile found'));
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error loading profile. Please try again.');
                },
                complete: function() {
                    $('.loader-box').hide();
                }
            });
        });

        function handleAjaxResponse(response, cat_id, btn_name) {
            if (!response.data || !response.data[0]) return;

            var pdfUrl = response.data[0].image_url;

            if (btn_name == 'vi') {
                window.open(pdfUrl, '_blank');
            } else if (btn_name == 'dwn') {
                downloadPDF(pdfUrl);
            }

            $('.pdf-container').css('display', 'flex');
        }

        // Download PDF function
        function downloadPDF(pdfUrl) {
            const link = document.createElement('a');
            link.href = pdfUrl;
            link.download = pdfUrl.split('/').pop();
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

    });
    </script>

    <!-- Loader for profile AJAX -->
    <div class="loader-box">
        <div class="loader-filter"></div>
    </div>

    <?php
}