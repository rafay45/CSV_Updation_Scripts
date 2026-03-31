<?php
/**
 * Plugin Name: Category Accordion & Form Injector
 * Description: Adds 5 accordion sections (Overview, Specifications, Videos, Reviews, Downloads) and contact form to first-level subcategory pages
 * Version: 1.0
 * Author: Wholesale Fencing
 */

// Prevent direct access
if (!defined('ABSPATH')) exit;

/**
 * Inject accordion sections and contact form after WooCommerce category content
 * This works WITH your existing template (doesn't override anything)
 *
 * Try multiple hooks to ensure it fires with Flatsome theme
 */
add_action('woocommerce_after_main_content', 'wsf_inject_category_accordion', 5);
add_action('woocommerce_after_shop_loop', 'wsf_inject_category_accordion', 99);
add_action('flatsome_after_category_description', 'wsf_inject_category_accordion', 10);
add_action('wp_footer', 'wsf_inject_category_accordion_footer', 5);

function wsf_inject_category_accordion() {
    // Only run on product category pages
    if (!is_tax('product_cat')) {
        return;
    }

    // Get current category
    $current_cat = get_queried_object();
    if (!$current_cat || !isset($current_cat->term_id)) {
        return;
    }

    $cat_id = $current_cat->term_id;

    // ═══════════════════════════════════════════════════════════════════════
    // CRITICAL CHECK: Only show on FIRST-LEVEL SUBCATEGORIES
    // ═══════════════════════════════════════════════════════════════════════
    // 1. Current category must have a parent (not a main category)
    // 2. Parent category must be a top-level category (parent's parent = 0)

    $show_accordion = false;

    if (!empty($current_cat->parent) && $current_cat->parent != 0) {
        // Get parent category
        $parent_cat = get_term($current_cat->parent, 'product_cat');

        // Check if parent is a top-level category (has no parent itself)
        if (!is_wp_error($parent_cat) && (empty($parent_cat->parent) || $parent_cat->parent == 0)) {
            $show_accordion = true;
        }
    }

    // If not a first-level subcategory, exit
    if (!$show_accordion) {
        return;
    }

    // ═══════════════════════════════════════════════════════════════════════
    // Get accordion data
    // ═══════════════════════════════════════════════════════════════════════
    $intro_text   = $current_cat->description;
    $video_ids    = get_term_meta($cat_id, 'category_video_ids', true);
    $review_link  = get_term_meta($cat_id, 'category_review_link', true);

    // Parse video IDs (comma-separated YouTube video IDs)
    $videos = array();
    if (!empty($video_ids)) {
        $videos = array_filter(array_map('trim', explode(',', $video_ids)));
    }

    ?>
    <!-- ══════════════════════════════════════════════════════════════════ -->
    <!-- WHOLESALE FENCING: CATEGORY ACCORDION SECTIONS -->
    <!-- ══════════════════════════════════════════════════════════════════ -->
    <div class="wf-category-accordion-wrapper" style="max-width: 1200px; margin: 40px auto 30px; padding: 0 20px;">

        <div class="wf-category-accordion" style="margin-bottom: 30px;">

            <!-- ═══════════════════════════════════════════════════════ -->
            <!-- 1. OVERVIEW -->
            <!-- ═══════════════════════════════════════════════════════ -->
            <div class="accordion-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden;">
                <div class="accordion-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wfToggleAccordion('overview')">
                    <span>Overview</span>
                    <span class="accordion-icon" style="font-size: 24px; font-weight: bold; color: #666;">∨</span>
                </div>
                <div class="accordion-content" id="accordion-overview" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <?php if (!empty($intro_text)): ?>
                        <?php echo wpautop(wp_kses_post($intro_text)); ?>
                    <?php else: ?>
                        <p style="color: #888;">No overview content available for this category.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════ -->
            <!-- 2. SPECIFICATIONS -->
            <!-- ═══════════════════════════════════════════════════════ -->
            <div class="accordion-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden;">
                <div class="accordion-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wfToggleAccordion('specifications')">
                    <span>Specifications</span>
                    <span class="accordion-icon" style="font-size: 24px; font-weight: bold; color: #666;">∨</span>
                </div>
                <div class="accordion-content" id="accordion-specifications" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <p style="color: #888;">Specifications content will be added here.</p>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════ -->
            <!-- 3. VIDEOS -->
            <!-- ═══════════════════════════════════════════════════════ -->
            <div class="accordion-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden;">
                <div class="accordion-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wfToggleAccordion('videos')">
                    <span>Videos</span>
                    <span class="accordion-icon" style="font-size: 24px; font-weight: bold; color: #666;">∨</span>
                </div>
                <div class="accordion-content" id="accordion-videos" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <?php if (!empty($videos)): ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                            <?php foreach ($videos as $video_id): ?>
                                <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                    <iframe
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                                        src="https://www.youtube.com/embed/<?php echo esc_attr($video_id); ?>"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                        loading="lazy">
                                    </iframe>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="color: #888;">No videos available for this category.</p>
                        <p style="font-size: 13px; color: #999;">Add YouTube video IDs in the category settings to display videos here.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════ -->
            <!-- 4. REVIEWS -->
            <!-- ═══════════════════════════════════════════════════════ -->
            <div class="accordion-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden;">
                <div class="accordion-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wfToggleAccordion('reviews')">
                    <span>Reviews</span>
                    <span class="accordion-icon" style="font-size: 24px; font-weight: bold; color: #666;">∨</span>
                </div>
                <div class="accordion-content" id="accordion-reviews" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <?php if (!empty($review_link)): ?>
                        <p style="margin-bottom: 15px;">See what our customers are saying about our products and services.</p>
                        <a href="<?php echo esc_url($review_link); ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           style="display: inline-block; background: #4285f4; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; transition: background 0.3s ease;"
                           onmouseover="this.style.background='#357ae8'"
                           onmouseout="this.style.background='#4285f4'">
                            View Google Reviews
                        </a>
                    <?php else: ?>
                        <p style="color: #888;">No review link configured for this category.</p>
                        <p style="font-size: 13px; color: #999;">Add a Google Review Link in the category settings.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════ -->
            <!-- 5. DOWNLOADS -->
            <!-- ═══════════════════════════════════════════════════════ -->
            <div class="accordion-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden;">
                <div class="accordion-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wfToggleAccordion('downloads')">
                    <span>Downloads</span>
                    <span class="accordion-icon" style="font-size: 24px; font-weight: bold; color: #666;">∨</span>
                </div>
                <div class="accordion-content" id="accordion-downloads" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <p style="color: #888;">Downloads section will be added here.</p>
                    <p style="font-size: 13px; color: #999;">Product catalogs, installation guides, and spec sheets coming soon.</p>
                </div>
            </div>

        </div>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- CONTACT FORM -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <div class="wf-contact-form" style="margin: 30px 0;">
            <?php echo do_shortcode('[wooaddon_from]'); ?>
        </div>

    </div><!-- .wf-category-accordion-wrapper -->

    <!-- JavaScript for Accordion Toggle -->
    <script>
    function wfToggleAccordion(section) {
        var content = document.getElementById('accordion-' + section);
        if (!content) return;

        var header = content.previousElementSibling;
        var icon = header.querySelector('.accordion-icon');
        var isOpen = content.style.display === 'block';

        // Close all other accordions
        var allContents = document.querySelectorAll('.wf-category-accordion .accordion-content');
        var allIcons = document.querySelectorAll('.wf-category-accordion .accordion-icon');

        allContents.forEach(function(item) {
            if (item !== content) item.style.display = 'none';
        });

        allIcons.forEach(function(ic) {
            if (ic !== icon) {
                ic.textContent = '∨';
                ic.style.color = '#666';
            }
        });

        // Toggle current accordion
        if (isOpen) {
            content.style.display = 'none';
            icon.textContent = '∨';
            icon.style.color = '#666';
        } else {
            content.style.display = 'block';
            icon.textContent = '∧';
            icon.style.color = '#327A1F';
        }
    }
    </script>

    <!-- Accordion Hover Styles -->
    <style>
    .wf-category-accordion .accordion-header:hover {
        background: #ebebeb !important;
    }
    .wf-category-accordion .accordion-header:hover .accordion-icon {
        color: #327A1F !important;
    }
    @media (max-width: 768px) {
        .wf-category-accordion .accordion-header {
            font-size: 16px !important;
            padding: 12px 16px !important;
        }
        .wf-category-accordion .accordion-content {
            padding: 16px !important;
        }
    }
    </style>
    <?php
}

/**
 * Fallback function that injects via footer if other hooks don't work
 * Uses JavaScript to inject the content at the right position
 */
function wsf_inject_category_accordion_footer() {
    // Only run on product category pages
    if (!is_tax('product_cat')) {
        return;
    }

    // Get current category
    $current_cat = get_queried_object();
    if (!$current_cat || !isset($current_cat->term_id)) {
        return;
    }

    $cat_id = $current_cat->term_id;

    // Check if first-level subcategory
    $show_accordion = false;
    if (!empty($current_cat->parent) && $current_cat->parent != 0) {
        $parent_cat = get_term($current_cat->parent, 'product_cat');
        if (!is_wp_error($parent_cat) && (empty($parent_cat->parent) || $parent_cat->parent == 0)) {
            $show_accordion = true;
        }
    }

    if (!$show_accordion) {
        return;
    }

    // Get accordion data
    $intro_text   = $current_cat->description;
    $video_ids    = get_term_meta($cat_id, 'category_video_ids', true);
    $review_link  = get_term_meta($cat_id, 'category_review_link', true);

    $videos = array();
    if (!empty($video_ids)) {
        $videos = array_filter(array_map('trim', explode(',', $video_ids)));
    }

    // Build accordion HTML
    ob_start();
    ?>
    <div id="wsf-accordion-inject" style="display: none; max-width: 1200px; margin: 40px auto 30px; padding: 0 20px;">

        <div class="wf-category-accordion" style="margin-bottom: 30px;">

            <!-- Overview -->
            <div class="accordion-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden;">
                <div class="accordion-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wfToggleAccordion('overview')">
                    <span>Overview</span>
                    <span class="accordion-icon" style="font-size: 24px; font-weight: bold; color: #666;">∨</span>
                </div>
                <div class="accordion-content" id="accordion-overview" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <?php if (!empty($intro_text)): ?>
                        <?php echo wpautop(wp_kses_post($intro_text)); ?>
                    <?php else: ?>
                        <p style="color: #888;">No overview content available for this category.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Specifications -->
            <div class="accordion-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden;">
                <div class="accordion-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wfToggleAccordion('specifications')">
                    <span>Specifications</span>
                    <span class="accordion-icon" style="font-size: 24px; font-weight: bold; color: #666;">∨</span>
                </div>
                <div class="accordion-content" id="accordion-specifications" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <p style="color: #888;">Specifications content will be added here.</p>
                </div>
            </div>

            <!-- Videos -->
            <div class="accordion-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden;">
                <div class="accordion-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wfToggleAccordion('videos')">
                    <span>Videos</span>
                    <span class="accordion-icon" style="font-size: 24px; font-weight: bold; color: #666;">∨</span>
                </div>
                <div class="accordion-content" id="accordion-videos" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <?php if (!empty($videos)): ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                            <?php foreach ($videos as $video_id): ?>
                                <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                    <iframe
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                                        src="https://www.youtube.com/embed/<?php echo esc_attr($video_id); ?>"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                        loading="lazy">
                                    </iframe>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="color: #888;">No videos available for this category.</p>
                        <p style="font-size: 13px; color: #999;">Add YouTube video IDs in the category settings to display videos here.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Reviews -->
            <div class="accordion-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden;">
                <div class="accordion-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wfToggleAccordion('reviews')">
                    <span>Reviews</span>
                    <span class="accordion-icon" style="font-size: 24px; font-weight: bold; color: #666;">∨</span>
                </div>
                <div class="accordion-content" id="accordion-reviews" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <?php if (!empty($review_link)): ?>
                        <p style="margin-bottom: 15px;">See what our customers are saying about our products and services.</p>
                        <a href="<?php echo esc_url($review_link); ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           style="display: inline-block; background: #4285f4; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; transition: background 0.3s ease;"
                           onmouseover="this.style.background='#357ae8'"
                           onmouseout="this.style.background='#4285f4'">
                            View Google Reviews
                        </a>
                    <?php else: ?>
                        <p style="color: #888;">No review link configured for this category.</p>
                        <p style="font-size: 13px; color: #999;">Add a Google Review Link in the category settings.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Downloads -->
            <div class="accordion-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden;">
                <div class="accordion-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wfToggleAccordion('downloads')">
                    <span>Downloads</span>
                    <span class="accordion-icon" style="font-size: 24px; font-weight: bold; color: #666;">∨</span>
                </div>
                <div class="accordion-content" id="accordion-downloads" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <p style="color: #888;">Downloads section will be added here.</p>
                    <p style="font-size: 13px; color: #999;">Product catalogs, installation guides, and spec sheets coming soon.</p>
                </div>
            </div>

        </div>

        <!-- Contact Form -->
        <div class="wf-contact-form" style="margin: 30px 0;">
            <?php echo do_shortcode('[wooaddon_from]'); ?>
        </div>

    </div>

    <script>
    // Inject accordion content after page loads
    document.addEventListener('DOMContentLoaded', function() {
        var accordionHTML = document.getElementById('wsf-accordion-inject');
        if (!accordionHTML) return;

        // Try multiple injection points (Flatsome theme compatibility)
        var targets = [
            document.querySelector('.shop-container'),
            document.querySelector('.category-page-content'),
            document.querySelector('.product-archive'),
            document.querySelector('.tax-product_cat .large-12'),
            document.querySelector('main.site-main'),
            document.querySelector('#content')
        ];

        for (var i = 0; i < targets.length; i++) {
            if (targets[i]) {
                accordionHTML.style.display = 'block';
                targets[i].appendChild(accordionHTML);
                console.log('WSF Accordion injected via:', targets[i].className || targets[i].tagName);
                break;
            }
        }
    });
    </script>
    <?php
    $html = ob_get_clean();
    echo $html;
}
