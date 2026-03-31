<?php
/**
 * Complete Subcategory Layout Handler
 * Shows: Accordions + Form BEFORE Products
 *
 * Add this to your theme's functions.php:
 * require_once get_stylesheet_directory() . '/subcategory_complete_layout.php';
 */

// Prevent direct access
if (!defined('ABSPATH')) exit;

// FILE LOADED SUCCESSFULLY - This will appear in debug.log
error_log('=====================================');
error_log('WSF: subcategory_complete_layout.php FILE LOADED!');
error_log('=====================================');

/**
 * Inject accordions using JavaScript insertion
 * This is the ONLY reliable way with Flatsome's AJAX category loading
 */
add_action('wp_footer', 'wsf_show_accordions_before_products', 20);

function wsf_show_accordions_before_products() {
    // DEBUG: Force display to check if file is loading
    error_log('WSF Accordion function called!');

    // Run on product category archive pages AND single product pages
    $is_category_page = is_product_category();
    $is_product_page = is_singular('product');

    if (!$is_category_page && !$is_product_page) {
        error_log('WSF: Not a category or product page');
        return;
    }

    error_log('WSF: IS a ' . ($is_category_page ? 'category' : 'product') . ' page!');

    // Static variable to prevent duplicate renders
    static $already_rendered = false;
    if ($already_rendered) {
        error_log('WSF: Already rendered, skipping');
        return;
    }
    $already_rendered = true;

    // For CATEGORY pages: Check if it's a subcategory (not main category)
    if ($is_category_page) {
        $current_cat = get_queried_object();
        if (!$current_cat || !isset($current_cat->term_id)) {
            return;
        }

        // Exclude main categories (categories with no parent)
        if (empty($current_cat->parent) || $current_cat->parent == 0) {
            error_log('WSF: This is a MAIN category (no parent) - not showing accordions');
            return;
        }

        $cat_id = $current_cat->term_id;
        error_log('WSF: This is a subcategory (parent: ' . $current_cat->parent . ') - rendering accordion JavaScript');
    }

    // For PRODUCT pages: Get category from product
    if ($is_product_page) {
        global $post;
        $product_cats = get_the_terms($post->ID, 'product_cat');

        if (!$product_cats || is_wp_error($product_cats)) {
            error_log('WSF: Product has no categories');
            return;
        }

        // Use the first category
        $current_cat = $product_cats[0];
        $cat_id = $current_cat->term_id;
        error_log('WSF: Product page - using category: ' . $current_cat->name . ' (ID: ' . $cat_id . ')');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // Get accordion data
    // ═══════════════════════════════════════════════════════════════════════
    $intro_text      = $current_cat->description;
    $video_ids       = get_term_meta($cat_id, 'category_video_ids', true);
    $review_link     = get_term_meta($cat_id, 'category_review_link', true);
    $specifications  = get_term_meta($cat_id, 'category_specifications', true);

    $videos = array();
    if (!empty($video_ids)) {
        $videos = array_filter(array_map('trim', explode(',', $video_ids)));
    }

    // DEBUG: Log accordion data
    error_log('WSF DEBUG - Category ID: ' . $cat_id);
    error_log('WSF DEBUG - Raw video_ids: ' . $video_ids);
    error_log('WSF DEBUG - Processed videos array: ' . print_r($videos, true));
    error_log('WSF DEBUG - Specifications: ' . ($specifications ? $specifications : 'EMPTY'));
    error_log('WSF DEBUG - Review Link: ' . ($review_link ? $review_link : 'EMPTY'));

    // Create accordion HTML
    ob_start();
    ?>
    <div class="wsf-category-accordion-section" style="width: 100%; max-width: 1200px; margin: 40px auto; padding: 20px; clear: both;">

        <div class="wsf-accordions" style="margin-bottom: 30px;">

            <!-- OVERVIEW -->
            <div class="wsf-acc-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden; background: #fff;">
                <div class="wsf-acc-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wsfToggleAcc('overview')">
                    <span>Overview</span>
                    <span class="wsf-acc-icon" style="font-size: 24px; font-weight: bold; color: #666;">∨</span>
                </div>
                <div class="wsf-acc-content" id="wsf-acc-overview" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <?php if (!empty($intro_text)): ?>
                        <?php echo wpautop(wp_kses_post($intro_text)); ?>
                    <?php else: ?>
                        <p style="color: #888;">No overview content available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- SPECIFICATIONS -->
            <div class="wsf-acc-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden; background: #fff;">
                <div class="wsf-acc-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wsfToggleAcc('specifications')">
                    <span>Specifications</span>
                    <span class="wsf-acc-icon" style="font-size: 24px; font-weight: bold; color: #666;">∨</span>
                </div>
                <div class="wsf-acc-content" id="wsf-acc-specifications" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <?php if (!empty($specifications)): ?>
                        <?php echo wpautop(wp_kses_post($specifications)); ?>
                    <?php else: ?>
                        <p style="color: #888;">No specifications available at this time.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- VIDEOS -->
            <div class="wsf-acc-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden; background: #fff;">
                <div class="wsf-acc-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wsfToggleAcc('videos')">
                    <span>Videos</span>
                    <span class="wsf-acc-icon" style="font-size: 24px; font-weight: bold; color: #666;">∨</span>
                </div>
                <div class="wsf-acc-content" id="wsf-acc-videos" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <?php if (!empty($videos)): ?>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                            <?php
                            $video_index = 0;
                            foreach ($videos as $video_id):
                                $clean_id = trim($video_id);
                                $video_index++;
                            ?>
                                <div class="wsf-video-container" data-video-id="<?php echo esc_attr($clean_id); ?>" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); background: #000; cursor: pointer;">
                                    <!-- Thumbnail with play button -->
                                    <img
                                        src="https://img.youtube.com/vi/<?php echo esc_attr($clean_id); ?>/maxresdefault.jpg"
                                        alt="Video thumbnail"
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
                                        onerror="this.src='https://img.youtube.com/vi/<?php echo esc_attr($clean_id); ?>/hqdefault.jpg';"
                                    >
                                    <!-- Play button overlay -->
                                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 68px; height: 48px; background: rgba(255,0,0,0.8); border-radius: 12px; pointer-events: none;">
                                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-35%, -50%); width: 0; height: 0; border-left: 18px solid white; border-top: 12px solid transparent; border-bottom: 12px solid transparent;"></div>
                                    </div>
                                    <!-- Hidden iframe (will be loaded on click) -->
                                    <div class="wsf-iframe-wrapper" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="color: #888;">No videos available for this category.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- REVIEWS -->
            <div class="wsf-acc-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden; background: #fff;">
                <div class="wsf-acc-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wsfToggleAcc('reviews')">
                    <span>Reviews</span>
                    <span class="wsf-acc-icon" style="font-size: 24px; font-weight: bold; color: #666;">∨</span>
                </div>
                <div class="wsf-acc-content" id="wsf-acc-reviews" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <?php if (!empty($review_link)): ?>
                        <p style="margin-bottom: 15px;">See what our customers are saying.</p>
                        <a href="<?php echo esc_url($review_link); ?>" target="_blank" rel="noopener" style="display: inline-block; background: #4285f4; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600;">View Google Reviews</a>
                    <?php else: ?>
                        <p style="color: #888;">No review link configured.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- DOWNLOADS -->
            <div class="wsf-acc-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden; background: #fff;">
                <div class="wsf-acc-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wsfToggleAcc('downloads')">
                    <span>Downloads</span>
                    <span class="wsf-acc-icon" style="font-size: 24px; font-weight: bold; color: #666;">∨</span>
                </div>
                <div class="wsf-acc-content" id="wsf-acc-downloads" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <p style="color: #888;">Downloads section will be added here.</p>
                </div>
            </div>

        </div>

        <!-- CONTACT FORM -->
        <div class="wsf-contact-form-wrapper" style="margin: 30px 0; padding: 20px; border-radius: 8px;">
            <h2 style="text-align: center; font-size: 28px; font-weight: 700; color: #333; margin-bottom: 15px;">Get Your Free Vinyl Fencing Quote</h2>
            <p style="text-align: center; font-size: 16px; color: #666; margin-bottom: 20px;">
                Please Fill Out This Form For A Free Quote. You Can Email Customer Service Directly at:<br>
                <a href="mailto:orders@wholesalefencing.com" style="color: #327A1F; text-decoration: none; font-weight: 600;">orders@wholesalefencing.com</a>
            </p>
            <?php echo do_shortcode('[wooaddon_from]'); ?>
        </div>

    </div>

    <style>
    .wsf-accordions .wsf-acc-header:hover { background: #ebebeb !important; }
    .wsf-accordions .wsf-acc-header:hover .wsf-acc-icon { color: #327A1F !important; }
    </style>
    <?php

    $accordion_html = ob_get_clean();

    // Inject accordion HTML using JavaScript after page loads
    ?>
    <script>
    // Define toggle function GLOBALLY so onclick can access it
    window.wsfToggleAcc = function(section) {
        var content = document.getElementById('wsf-acc-' + section);
        if (!content) return;

        var header = content.previousElementSibling;
        var icon = header.querySelector('.wsf-acc-icon');
        var isOpen = content.style.display === 'block';

        document.querySelectorAll('.wsf-accordions .wsf-acc-content').forEach(function(item) {
            if (item !== content) item.style.display = 'none';
        });

        document.querySelectorAll('.wsf-accordions .wsf-acc-icon').forEach(function(ic) {
            if (ic !== icon) {
                ic.textContent = '∨';
                ic.style.color = '#666';
            }
        });

        if (isOpen) {
            content.style.display = 'none';
            icon.textContent = '∨';
            icon.style.color = '#666';
        } else {
            content.style.display = 'block';
            icon.textContent = '∧';
            icon.style.color = '#327A1F';
        }
    };

    // YouTube video click handler - load iframe when thumbnail is clicked
    window.wsfLoadVideo = function(container) {
        var videoId = container.getAttribute('data-video-id');
        if (!videoId) return;

        var iframeWrapper = container.querySelector('.wsf-iframe-wrapper');
        if (!iframeWrapper) return;

        // Create and insert iframe
        var iframe = document.createElement('iframe');
        iframe.style.cssText = 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;';
        iframe.src = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0&modestbranding=1';
        iframe.title = 'YouTube video player';
        iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
        iframe.setAttribute('allowfullscreen', '');

        iframeWrapper.appendChild(iframe);
        iframeWrapper.style.display = 'block';

        // Hide thumbnail
        container.querySelector('img').style.display = 'none';
        container.querySelector('div[style*="border-left"]').parentElement.style.display = 'none';

        console.log('WSF: Loaded YouTube video: ' + videoId);
    };

    (function() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', insertAccordions);
        } else {
            insertAccordions();
        }

        function insertAccordions() {
            console.log('WSF: Trying to insert accordions...');

            // CHECK: Does this page have subcategories/category cards section?
            var hasSubcategories = false;

            // Method 1: Check for #wsf-sub-categories
            if (document.getElementById('wsf-sub-categories')) {
                hasSubcategories = true;
                console.log('WSF: ✓ Found #wsf-sub-categories section');
            }

            // Method 2: Check for #wsf-product-categories (category cards section)
            if (!hasSubcategories && document.getElementById('wsf-product-categories')) {
                hasSubcategories = true;
                console.log('WSF: ✓ Found #wsf-product-categories section');
            }

            // Method 3: Look for headings containing "sub categor" or "product categor"
            if (!hasSubcategories) {
                var allHeadings = document.querySelectorAll('h1, h2, h3, h4, .section-title');
                for (var i = 0; i < allHeadings.length; i++) {
                    var text = allHeadings[i].textContent.toLowerCase();
                    if (text.includes('sub categor') || text.includes('product categor')) {
                        hasSubcategories = true;
                        console.log('WSF: ✓ Found category heading: "' + allHeadings[i].textContent.trim() + '"');
                        break;
                    }
                }
            }

            // Method 4: Check for .dpc-card-grid with .dpc-cat-card (category cards)
            if (!hasSubcategories) {
                var cardGrid = document.querySelector('.dpc-card-grid');
                if (cardGrid && cardGrid.querySelector('.dpc-cat-card')) {
                    hasSubcategories = true;
                    console.log('WSF: ✓ Found .dpc-card-grid with category cards');
                }
            }

            if (!hasSubcategories) {
                console.log('WSF: ✗ No subcategories found - this is a product listing page');
                return;
            }

            console.log('WSF: ✓ Subcategories detected - showing accordions');

            // FIRST: Try to find section with id="wsf-product-categories"
            var productCatSection = document.getElementById('wsf-product-categories');
            if (productCatSection) {
                console.log('WSF: ✓ Found #wsf-product-categories section!');
                productCatSection.insertAdjacentHTML('beforebegin', <?php echo json_encode($accordion_html); ?>);
                console.log('WSF: ✅ Accordions inserted BEFORE Product Category section!');
                attachVideoHandlers();
                return;
            }

            console.log('WSF: #wsf-product-categories not found, trying other methods...');

            // Find ALL .row elements to locate "Vinyl Sub Categories" section
            var allRows = document.querySelectorAll('.row');
            var subCatRow = null;

            // Look for the row containing subcategory title
            for (var i = 0; i < allRows.length; i++) {
                var headings = allRows[i].querySelectorAll('h2, h3, .section-title');
                for (var j = 0; j < headings.length; j++) {
                    var text = headings[j].textContent.trim().toLowerCase();
                    if (text.includes('sub categories') || text.includes('subcategories')) {
                        console.log('WSF: Found Sub Categories section!');
                        subCatRow = allRows[i];
                        break;
                    }
                }
                if (subCatRow) break;
            }

            // If found, insert AFTER the subcategories section
            if (subCatRow) {
                console.log('WSF: Inserting after Sub Categories section');
                subCatRow.insertAdjacentHTML('afterend', <?php echo json_encode($accordion_html); ?>);
                console.log('WSF: ✅ Accordions inserted after subcategories!');
                attachVideoHandlers();
                return;
            }

            // Fallback 1: Look for products grid with subcategory items
            var productGrid = document.querySelector('.product-category');
            if (!productGrid) {
                productGrid = document.querySelector('.products.row');
            }

            if (productGrid) {
                // Check if this grid has subcategory cards (not products)
                var hasSubcats = productGrid.querySelector('.product-small.col') !== null;
                if (hasSubcats) {
                    console.log('WSF: Found subcategory grid, inserting after it');
                    productGrid.insertAdjacentHTML('afterend', <?php echo json_encode($accordion_html); ?>);
                    console.log('WSF: ✅ Accordions inserted!');
                    attachVideoHandlers();
                    return;
                }
            }

            // Fallback 2: Insert before "Product Category" heading
            var productCatHeading = null;
            var allHeadings = document.querySelectorAll('h2, h3, .section-title');
            for (var k = 0; k < allHeadings.length; k++) {
                if (allHeadings[k].textContent.trim().toLowerCase().includes('product category')) {
                    productCatHeading = allHeadings[k];
                    break;
                }
            }

            if (productCatHeading) {
                console.log('WSF: Inserting before Product Category section');
                // Find parent row
                var parent = productCatHeading.parentElement;
                while (parent && !parent.classList.contains('row')) {
                    parent = parent.parentElement;
                }
                if (parent) {
                    parent.insertAdjacentHTML('beforebegin', <?php echo json_encode($accordion_html); ?>);
                    console.log('WSF: ✅ Accordions inserted!');
                    attachVideoHandlers();
                    return;
                }
            }

            // Final fallback: append to shop container
            console.log('WSF: Using final fallback position');
            var shopContainer = document.querySelector('.shop-container');
            if (shopContainer) {
                shopContainer.insertAdjacentHTML('beforeend', <?php echo json_encode($accordion_html); ?>);
            } else {
                document.querySelector('main').insertAdjacentHTML('beforeend', <?php echo json_encode($accordion_html); ?>);
            }
            console.log('WSF: ✅ Accordions inserted (fallback)');
            attachVideoHandlers();
        }

        // Attach click handlers to video thumbnails
        function attachVideoHandlers() {
            console.log('WSF: Attaching video click handlers...');
            var videoContainers = document.querySelectorAll('.wsf-video-container');
            console.log('WSF: Found ' + videoContainers.length + ' video containers');

            videoContainers.forEach(function(container) {
                container.addEventListener('click', function() {
                    console.log('WSF: Video thumbnail clicked');
                    window.wsfLoadVideo(container);
                });
            });
        }

        // URL CHANGE DETECTION: Hide/Show accordions when URL changes (AJAX navigation)
        function checkAccordionVisibility() {
            var accordionSection = document.querySelector('.wsf-category-accordion-section');
            if (!accordionSection) return;

            // Check if page has subcategories/category cards
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

            if (!hasSubcategories) {
                console.log('WSF: Hiding accordions (no subcategories - product listing page)');
                accordionSection.style.display = 'none';
            } else {
                console.log('WSF: Showing accordions (subcategories found)');
                accordionSection.style.display = 'block';
            }
        }

        // Monitor URL changes (for AJAX navigation)
        var lastUrl = window.location.href;
        setInterval(function() {
            if (window.location.href !== lastUrl) {
                lastUrl = window.location.href;
                console.log('WSF: URL changed to: ' + lastUrl);
                setTimeout(checkAccordionVisibility, 500); // Wait for content to load
            }
        }, 500);
    })();
    </script>
    <?php
}
