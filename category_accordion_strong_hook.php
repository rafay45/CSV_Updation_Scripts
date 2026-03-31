// ==========================================
// Category Accordion Dropdowns - STRONG HOOK VERSION
// Multiple hooks for maximum compatibility
// ==========================================

// Try multiple WooCommerce hooks for better compatibility
if (!function_exists('wf_add_category_accordion_multiple_hooks')) {
    // Hook 1: After archive description (standard WooCommerce)
    add_action('woocommerce_archive_description', 'wf_add_category_accordion_multiple_hooks', 99);

    // Hook 2: Before main content (backup)
    add_action('woocommerce_before_main_content', 'wf_add_category_accordion_multiple_hooks', 99);

    // Hook 3: After shop loop (another backup)
    add_action('woocommerce_after_shop_loop', 'wf_add_category_accordion_multiple_hooks', 5);

    function wf_add_category_accordion_multiple_hooks() {
        // Prevent multiple executions
        static $executed = false;
        if ($executed) {
            return;
        }

        if (!is_product_category()) {
            return;
        }

        $term = get_queried_object();
        $term_id = $term->term_id;

        // Check if this category has children (sub-categories)
        $child_cats = get_terms(array(
            'taxonomy'   => 'product_cat',
            'parent'     => $term_id,
            'hide_empty' => true,
        ));
        $has_children = !empty($child_cats) && !is_wp_error($child_cats);

        // Only show on categories with children
        if (!$has_children) {
            return;
        }

        // Mark as executed to prevent duplicates
        $executed = true;

        // Get data for dropdowns
        $intro_text = get_term_meta($term_id, 'wsf_cat_intro_text', true);
        $videos = function_exists('wf_get_category_videos') ? wf_get_category_videos($term_id) : array();
        $review_link = function_exists('wf_get_category_review_link') ? wf_get_category_review_link($term_id) : '';
        ?>

        <!-- WF CATEGORY ACCORDION -->
        <div class="wf-category-accordion" style="margin: 30px auto; max-width: 1200px; padding: 0 20px;">

            <!-- Overview -->
            <div class="accordion-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 4px;">
                <div class="accordion-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700;" onclick="wfToggleAccordion('overview')">
                    <span>Overview</span>
                    <span class="accordion-icon" style="font-size: 24px; font-weight: bold;">∨</span>
                </div>
                <div class="accordion-content" id="accordion-overview" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <?php if (!empty($intro_text)): ?>
                        <?php echo wpautop($intro_text); ?>
                    <?php else: ?>
                        <p>No overview available for this category.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Specifications -->
            <div class="accordion-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 4px;">
                <div class="accordion-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700;" onclick="wfToggleAccordion('specifications')">
                    <span>Specifications</span>
                    <span class="accordion-icon" style="font-size: 24px; font-weight: bold;">∨</span>
                </div>
                <div class="accordion-content" id="accordion-specifications" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <div style="background: #e8f5e9; border-left: 4px solid #4caf50; padding: 20px; text-align: center; color: #2e7d32; font-weight: 600; font-size: 16px;">
                        This Data is working on existing sub category pages.
                    </div>
                </div>
            </div>

            <!-- Videos -->
            <div class="accordion-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 4px;">
                <div class="accordion-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700;" onclick="wfToggleAccordion('videos')">
                    <span>Videos</span>
                    <span class="accordion-icon" style="font-size: 24px; font-weight: bold;">∨</span>
                </div>
                <div class="accordion-content" id="accordion-videos" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <?php if (!empty($videos)): ?>
                        <div class="category-videos" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                            <?php foreach ($videos as $video_id): ?>
                                <div class="video-wrapper" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                    <iframe
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                                        src="https://www.youtube.com/embed/<?php echo esc_attr($video_id); ?>"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No videos available for this category.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Reviews -->
            <div class="accordion-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 4px;">
                <div class="accordion-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700;" onclick="wfToggleAccordion('reviews')">
                    <span>Reviews</span>
                    <span class="accordion-icon" style="font-size: 24px; font-weight: bold;">∨</span>
                </div>
                <div class="accordion-content" id="accordion-reviews" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <?php if (!empty($review_link)): ?>
                        <div style="padding: 20px; background: #fff; border-left: 4px solid #4285f4; border-radius: 4px;">
                            <h3 style="margin-top: 0; color: #333;">Customer Reviews</h3>
                            <p style="font-size: 15px; line-height: 1.6; color: #666;">See what our customers are saying about our products and services.</p>
                            <a href="<?php echo esc_url($review_link); ?>" target="_blank" rel="noopener" style="display: inline-block; background: #4285f4; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: 600; margin-top: 10px;">
                                View Google Reviews →
                            </a>
                        </div>
                    <?php else: ?>
                        <p>No reviews available for this category.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Downloads -->
            <div class="accordion-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 4px;">
                <div class="accordion-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700;" onclick="wfToggleAccordion('downloads')">
                    <span>Downloads</span>
                    <span class="accordion-icon" style="font-size: 24px; font-weight: bold;">∨</span>
                </div>
                <div class="accordion-content" id="accordion-downloads" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <div style="background: #e8f5e9; border-left: 4px solid #4caf50; padding: 20px; text-align: center; color: #2e7d32; font-weight: 600; font-size: 16px;">
                        This Data is working on existing sub category pages.
                    </div>
                </div>
            </div>

        </div>

        <!-- CONTACT FORM -->
        <div class="wf-contact-form" style="margin: 40px auto; max-width: 1200px; padding: 0 20px;">
            <?php echo do_shortcode('[wooaddon_from]'); ?>
        </div>

        <!-- JavaScript for Accordion -->
        <script>
        if (typeof wfToggleAccordion === 'undefined') {
            function wfToggleAccordion(section) {
                var content = document.getElementById('accordion-' + section);
                var allContents = document.querySelectorAll('.accordion-content');
                var allIcons = document.querySelectorAll('.accordion-icon');

                // Close all except clicked
                allContents.forEach(function(item) {
                    if (item.id !== 'accordion-' + section) {
                        item.style.display = 'none';
                    }
                });

                // Reset all icons
                allIcons.forEach(function(icon) {
                    icon.textContent = '∨';
                });

                // Toggle clicked section
                if (content.style.display === 'none' || content.style.display === '') {
                    content.style.display = 'block';
                    event.target.closest('.accordion-header').querySelector('.accordion-icon').textContent = '∧';
                } else {
                    content.style.display = 'none';
                    event.target.closest('.accordion-header').querySelector('.accordion-icon').textContent = '∨';
                }
            }
        }
        </script>

        <?php
    }
}
