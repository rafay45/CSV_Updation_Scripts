<?php
// ==========================================
// WooCommerce Category Custom Fields - Videos & Reviews
// ==========================================

// Add custom fields to category ADD form
if (!function_exists('wf_add_category_custom_fields')) {
    add_action('product_cat_add_form_fields', 'wf_add_category_custom_fields', 10, 2);
    function wf_add_category_custom_fields() {
        ?>
        <div class="form-field">
            <label for="category_video_ids">Video IDs (YouTube)</label>
            <textarea name="category_video_ids" id="category_video_ids" rows="3"></textarea>
            <p class="description">Enter YouTube video IDs separated by commas (e.g., abc123, def456, ghi789). These will be displayed in the Videos tab.</p>
        </div>

        <div class="form-field">
            <label for="category_review_link">Google Review Link</label>
            <input type="text" name="category_review_link" id="category_review_link">
            <p class="description">Enter your Google My Business review link for this category.</p>
        </div>

        <div class="form-field">
            <label for="category_specifications">Specifications</label>
            <textarea name="category_specifications" id="category_specifications" rows="5"></textarea>
            <p class="description">Enter specifications for this category. You can use HTML for formatting.</p>
        </div>
        <?php
    }
}

// Add custom fields to category EDIT form
if (!function_exists('wf_edit_category_custom_fields')) {
    add_action('product_cat_edit_form_fields', 'wf_edit_category_custom_fields', 10, 2);
    function wf_edit_category_custom_fields($term) {
        $term_id = $term->term_id;
        $video_ids = get_term_meta($term_id, 'category_video_ids', true);
        $review_link = get_term_meta($term_id, 'category_review_link', true);
        ?>
        <tr class="form-field">
            <th scope="row">
                <label for="category_video_ids">Video IDs (YouTube)</label>
            </th>
            <td>
                <textarea name="category_video_ids" id="category_video_ids" rows="3" style="width: 100%;"><?php echo esc_attr($video_ids); ?></textarea>
                <p class="description">Enter YouTube video IDs separated by commas (e.g., abc123, def456, ghi789). These will be displayed in the Videos tab.</p>
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row">
                <label for="category_review_link">Google Review Link</label>
            </th>
            <td>
                <input type="text" name="category_review_link" id="category_review_link" value="<?php echo esc_attr($review_link); ?>" style="width: 100%;">
                <p class="description">Enter your Google My Business review link for this category.</p>
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row">
                <label for="category_specifications">Specifications</label>
            </th>
            <td>
                <textarea name="category_specifications" id="category_specifications" rows="5" style="width: 100%;"><?php echo esc_textarea(get_term_meta($term_id, 'category_specifications', true)); ?></textarea>
                <p class="description">Enter specifications for this category. You can use HTML for formatting.</p>
            </td>
        </tr>
        <?php
    }
}

// Save custom fields when category is created
if (!function_exists('wf_save_category_custom_fields_create')) {
    add_action('created_product_cat', 'wf_save_category_custom_fields_create', 10, 2);
    function wf_save_category_custom_fields_create($term_id) {
        if (isset($_POST['category_video_ids'])) {
            update_term_meta($term_id, 'category_video_ids', sanitize_textarea_field($_POST['category_video_ids']));
        }
        if (isset($_POST['category_review_link'])) {
            update_term_meta($term_id, 'category_review_link', esc_url_raw($_POST['category_review_link']));
        }
        if (isset($_POST['category_specifications'])) {
            update_term_meta($term_id, 'category_specifications', wp_kses_post($_POST['category_specifications']));
        }
    }
}

// Save custom fields when category is edited
if (!function_exists('wf_save_category_custom_fields_edit')) {
    add_action('edited_product_cat', 'wf_save_category_custom_fields_edit', 10, 2);
    function wf_save_category_custom_fields_edit($term_id) {
        if (isset($_POST['category_video_ids'])) {
            update_term_meta($term_id, 'category_video_ids', sanitize_textarea_field($_POST['category_video_ids']));
        }
        if (isset($_POST['category_review_link'])) {
            update_term_meta($term_id, 'category_review_link', esc_url_raw($_POST['category_review_link']));
        }
        if (isset($_POST['category_specifications'])) {
            update_term_meta($term_id, 'category_specifications', wp_kses_post($_POST['category_specifications']));
        }
    }
}

// Helper function to get category video IDs as array
if (!function_exists('wf_get_category_videos')) {
    function wf_get_category_videos($term_id) {
        $video_ids = get_term_meta($term_id, 'category_video_ids', true);
        if (empty($video_ids)) {
            return array();
        }
        // Split by comma and trim whitespace
        $videos = array_map('trim', explode(',', $video_ids));
        return array_filter($videos); // Remove empty values
    }
}

// Helper function to get category review link
if (!function_exists('wf_get_category_review_link')) {
    function wf_get_category_review_link($term_id) {
        return get_term_meta($term_id, 'category_review_link', true);
    }
}

// Helper function to get category specifications
if (!function_exists('wf_get_category_specifications')) {
    function wf_get_category_specifications($term_id) {
        return get_term_meta($term_id, 'category_specifications', true);
    }
}

// Display videos on category page (example usage)
if (!function_exists('wf_display_category_videos')) {
    function wf_display_category_videos($term_id) {
        $videos = wf_get_category_videos($term_id);
        if (empty($videos)) {
            echo '<p>No videos available for this category.</p>';
            return;
        }

        echo '<div class="category-videos" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;">';
        foreach ($videos as $video_id) {
            ?>
            <div class="video-wrapper" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                <iframe
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                    src="https://www.youtube.com/embed/<?php echo esc_attr($video_id); ?>"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
            <?php
        }
        echo '</div>';
    }
}

// Display review link on category page (example usage)
if (!function_exists('wf_display_category_review_link')) {
    function wf_display_category_review_link($term_id) {
        $review_link = wf_get_category_review_link($term_id);
        if (empty($review_link)) {
            return;
        }
        ?>
        <div class="category-reviews" style="margin: 20px 0; padding: 20px; background: #f9f9f9; border-left: 4px solid #0073aa;">
            <h3 style="margin-top: 0;">Customer Reviews</h3>
            <p>See what our customers are saying about our products and services.</p>
            <a href="<?php echo esc_url($review_link); ?>" target="_blank" rel="noopener" class="button button-primary" style="background: #4285f4; border-color: #4285f4; text-decoration: none; padding: 10px 20px; display: inline-block; color: white; border-radius: 4px;">
                View Google Reviews
            </a>
        </div>
        <?php
    }
}
