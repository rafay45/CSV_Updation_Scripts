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

        // IMPORTANT: Get the FIRST-LEVEL subcategory (not deeper levels)
        // If user navigates from Privacy -> Gate Hardware, we want Privacy's data, not Gate Hardware's
        $cat_id = $current_cat->term_id;
        $parent_id = $current_cat->parent;

        // Traverse up to find the first-level subcategory (direct child of main category)
        while ($parent_id != 0) {
            $parent_cat = get_term($parent_id, 'product_cat');
            if (!$parent_cat || is_wp_error($parent_cat)) {
                break;
            }

            // If parent's parent is 0, then current cat_id is the first-level subcategory
            if ($parent_cat->parent == 0) {
                // cat_id is already the first-level subcategory
                error_log('WSF: First-level subcategory: ' . $current_cat->name . ' (ID: ' . $cat_id . ')');
                break;
            }

            // Move up one level
            $cat_id = $parent_id;
            $parent_id = $parent_cat->parent;
        }

        error_log('WSF: Using category data from: ID ' . $cat_id . ' (parent: ' . $parent_id . ')');
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
        $parent_id = $current_cat->parent;

        error_log('WSF: Product page - category: ' . $current_cat->name . ' (ID: ' . $cat_id . ', parent: ' . $parent_id . ')');

        // Traverse up to find the first-level subcategory (same as category pages)
        while ($parent_id != 0) {
            $parent_cat = get_term($parent_id, 'product_cat');
            if (!$parent_cat || is_wp_error($parent_cat)) {
                break;
            }

            // If parent's parent is 0, then cat_id is the first-level subcategory
            if ($parent_cat->parent == 0) {
                error_log('WSF: Product page - First-level subcategory: ID ' . $cat_id);
                break;
            }

            // Move up one level
            $cat_id = $parent_id;
            $parent_id = $parent_cat->parent;
        }

        error_log('WSF: Product page - Using category data from: ID ' . $cat_id);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // Get accordion data from the FIRST-LEVEL subcategory (not current category)
    // ═══════════════════════════════════════════════════════════════════════
    // Get the category object for the first-level subcategory to fetch its description
    $first_level_cat = get_term($cat_id, 'product_cat');
    $intro_text      = $first_level_cat ? $first_level_cat->description : '';
    $video_ids       = get_term_meta($cat_id, 'category_video_ids', true);
    $review_link     = get_term_meta($cat_id, 'category_review_link', true);
    $specifications  = get_term_meta($cat_id, 'category_specifications', true);
    $pdf_downloads   = get_term_meta($cat_id, 'category_pdf_downloads', true);

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

    // DEBUG: Log accordion data
    error_log('WSF DEBUG - Category ID: ' . $cat_id);
    error_log('WSF DEBUG - Raw video_ids: ' . $video_ids);
    error_log('WSF DEBUG - Processed videos array: ' . print_r($videos, true));
    error_log('WSF DEBUG - Specifications: ' . ($specifications ? $specifications : 'EMPTY'));
    error_log('WSF DEBUG - Review Link: ' . ($review_link ? $review_link : 'EMPTY'));

    // Create accordion HTML
    ob_start();
    ?>
    <div class="wsf-category-accordion-section" style="width: 100%; max-width: 1200px; margin-top: -70px; padding: 20px; clear: both;">

        <div class="wsf-accordions" style="margin-bottom: 30px;">

            <!-- OVERVIEW -->
            <div class="wsf-acc-item" style="border: 1px solid #e0e0e0; margin-bottom: 10px; border-radius: 6px; overflow: hidden; background: #fff;">
                <div class="wsf-acc-header" style="background: #f5f5f5; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 700; color: #333;" onclick="wsfToggleAcc('overview')">
                    <span>Overview</span>
                    <span class="wsf-acc-icon" style="display: inline-flex; align-items: center; transition: transform 0.3s ease;">
                        <svg width="30" height="30" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 7.5L10 12.5L15 7.5" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
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
                    <span class="wsf-acc-icon" style="display: inline-flex; align-items: center; transition: transform 0.3s ease;">
                        <svg width="30" height="30" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 7.5L10 12.5L15 7.5" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
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
                    <span class="wsf-acc-icon" style="display: inline-flex; align-items: center; transition: transform 0.3s ease;">
                        <svg width="30" height="30" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 7.5L10 12.5L15 7.5" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
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
                    <span class="wsf-acc-icon" style="display: inline-flex; align-items: center; transition: transform 0.3s ease;">
                        <svg width="30" height="30" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 7.5L10 12.5L15 7.5" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
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
                    <span class="wsf-acc-icon" style="display: inline-flex; align-items: center; transition: transform 0.3s ease;">
                        <svg width="30" height="30" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 7.5L10 12.5L15 7.5" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
                <div class="wsf-acc-content" id="wsf-acc-downloads" style="display: none; padding: 20px; background: #f9f9f9; border-top: 1px solid #e0e0e0;">
                    <?php if (!empty($pdfs)): ?>
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <?php foreach ($pdfs as $pdf):
                                // Create download proxy URL
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

        <!-- CONTACT FORM -->
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

    </div>

    <style>
    .wsf-accordions .wsf-acc-header:hover { background: #ebebeb !important; }
    .wsf-accordions .wsf-acc-header:hover .wsf-acc-icon { color: #327A1F !important; }
    .wsf-pdf-link:hover { color: #000 !important; }
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
        var svg = icon.querySelector('svg path');
        var isOpen = content.style.display === 'block';

        // Close all other accordions
        document.querySelectorAll('.wsf-accordions .wsf-acc-content').forEach(function(item) {
            if (item !== content) item.style.display = 'none';
        });

        // Reset all other icons
        document.querySelectorAll('.wsf-accordions .wsf-acc-icon').forEach(function(ic) {
            if (ic !== icon) {
                ic.style.transform = 'rotate(0deg)';
                var otherSvg = ic.querySelector('svg path');
                if (otherSvg) otherSvg.setAttribute('stroke', '#666');
            }
        });

        // Toggle current accordion
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

            // Method 5: For PRODUCT pages - check for category products section
            if (!hasSubcategories) {
                var relatedProductsHeadings = document.querySelectorAll('h2, h3, h4');
                for (var i = 0; i < relatedProductsHeadings.length; i++) {
                    var text = relatedProductsHeadings[i].textContent.toLowerCase();
                    if (text.includes('related product') || text.includes('you may also like')) {
                        hasSubcategories = true;
                        console.log('WSF: ✓ Found related products section on product page');
                        break;
                    }
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

            // SPECIAL: For PRODUCT pages - insert after FIRST .wsf-term-group (subcategory section)
            var isProductPage = document.body.classList.contains('single-product');
            if (isProductPage) {
                console.log('WSF: This is a PRODUCT page - looking for .wsf-term-group section');

                // Method 1: Look for .wsf-term-group (subcategory section on product page)
                var termGroups = document.querySelectorAll('.wsf-term-group');
                if (termGroups.length > 0) {
                    var firstTermGroup = termGroups[0]; // Use the FIRST one only
                    console.log('WSF: ✓ Found .wsf-term-group sections (total: ' + termGroups.length + '), using the FIRST one');
                    console.log('WSF: Inserting accordions AFTER FIRST .wsf-term-group');
                    firstTermGroup.insertAdjacentHTML('afterend', <?php echo json_encode($accordion_html); ?>);
                    console.log('WSF: ✅ Accordions inserted on product page!');
                    attachVideoHandlers();
                    return;
                }

                // Method 2: Look for category products grid (subcategories on product page)
                var categoryGrid = document.querySelector('.dpc-card-grid');
                if (categoryGrid && categoryGrid.querySelector('.dpc-cat-card')) {
                    console.log('WSF: ✓ Found category grid on product page');
                    // Find the parent section/container
                    var gridParent = categoryGrid.parentElement;
                    while (gridParent && !gridParent.classList.contains('section')) {
                        gridParent = gridParent.parentElement;
                    }
                    if (gridParent) {
                        console.log('WSF: Inserting accordions AFTER category grid section');
                        gridParent.insertAdjacentHTML('afterend', <?php echo json_encode($accordion_html); ?>);
                        console.log('WSF: ✅ Accordions inserted on product page!');
                        attachVideoHandlers();
                        return;
                    }
                }

                // Method 3: Look for "Related Products" or "You may also like" section
                var relatedSection = null;
                var allSections = document.querySelectorAll('.related.products, section');

                for (var i = 0; i < allSections.length; i++) {
                    var heading = allSections[i].querySelector('h2, h3');
                    if (heading) {
                        var headingText = heading.textContent.toLowerCase();
                        if (headingText.includes('related product') || headingText.includes('you may also like')) {
                            relatedSection = allSections[i];
                            console.log('WSF: ✓ Found related products section: "' + heading.textContent.trim() + '"');
                            break;
                        }
                    }
                }

                if (relatedSection) {
                    console.log('WSF: Inserting accordions AFTER related products section');
                    relatedSection.insertAdjacentHTML('afterend', <?php echo json_encode($accordion_html); ?>);
                    console.log('WSF: ✅ Accordions inserted on product page!');
                    attachVideoHandlers();
                    return;
                }

                // Method 4: Look for any products grid with .row.row-small
                var productRows = document.querySelectorAll('.row.row-small');
                if (productRows.length > 0) {
                    var lastProductRow = productRows[productRows.length - 1];
                    console.log('WSF: Found product row grid, inserting after it');
                    lastProductRow.insertAdjacentHTML('afterend', <?php echo json_encode($accordion_html); ?>);
                    console.log('WSF: ✅ Accordions inserted on product page!');
                    attachVideoHandlers();
                    return;
                }
            }

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

        // Attach click handlers to video thumbnails and PDF downloads
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

            // PDF downloads handled by download-pdf.php proxy
            // No JavaScript needed - server forces download with Content-Disposition header

            // Make subcategory cards toggleable
            makeSubcategoryCardsToggleable();
        }

        // Make subcategory cards (in #wsf-sub-categories) toggleable
        function makeSubcategoryCardsToggleable() {
            // Try to find subcategory section - #wsf-sub-categories (category pages)
            var subCatSection = document.getElementById('wsf-sub-categories');

            if (!subCatSection) {
                console.log('WSF: #wsf-sub-categories section not found');
                // For product pages, check for .wsf-term-group
                var termGroups = document.querySelectorAll('.wsf-term-group');
                if (termGroups.length > 0) {
                    console.log('WSF: Found ' + termGroups.length + ' .wsf-term-group sections (product page)');
                    // Make FIRST one accordion, rest just centered
                    subCatSection = termGroups[0];
                    console.log('WSF: Using FIRST .wsf-term-group for accordion');
                    // Center the rest (skip first)
                    for (var i = 1; i < termGroups.length; i++) {
                        var h = termGroups[i].querySelector('h2, h3, h1');
                        if (h && !h.hasAttribute('data-wsf-centered')) {
                            h.style.textAlign = 'center';
                            h.setAttribute('data-wsf-centered', 'true');
                            console.log('WSF: ✓ Centered heading ' + i + ': ' + h.textContent.trim());
                        }
                    }
                } else {
                    console.log('WSF: No subcategory sections found');
                    return;
                }
            } else {
                console.log('WSF: Found #wsf-sub-categories section (category page)');
            }

            var heading = subCatSection.querySelector('h2, h3, h1');
            if (!heading) {
                console.log('WSF: No heading found in subcategory section');
                return;
            }

            // Check if already initialized
            if (heading.hasAttribute('data-wsf-initialized')) {
                console.log('WSF: Subcategory cards toggle already initialized');
                return;
            }

            // Find the subcategory cards grid/container (usually next sibling)
            var cardsContainer = null;
            var nextEl = subCatSection.nextElementSibling;

            // Try to find cards container - could be .dpc-card-grid, .category-grid, or any grid
            if (nextEl && (nextEl.classList.contains('dpc-card-grid') || nextEl.classList.contains('category-grid'))) {
                cardsContainer = nextEl;
            } else {
                // Search within the section itself
                cardsContainer = subCatSection.querySelector('.dpc-card-grid, .category-grid, [class*="grid"]');
            }

            if (!cardsContainer) {
                console.log('WSF: Cards container not found');
                return;
            }

            // Save original text
            var headingText = heading.textContent;

            // Style heading as accordion
            heading.style.cursor = 'pointer';
            heading.style.display = 'flex';
            heading.style.justifyContent = 'center';
            heading.style.alignItems = 'center';
            heading.style.padding = '15px 20px';
            heading.style.background = '#f5f5f5';
            heading.style.borderRadius = '6px';
            heading.style.marginBottom = '20px';
            heading.style.position = 'relative';

            // Add SVG icon
            var iconHTML = '<span class="wsf-subcat-cards-toggle-icon" style="position: absolute; right: 20px; display: inline-flex; align-items: center; transition: transform 0.3s ease;"><svg width="30" height="30" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 7.5L10 12.5L15 7.5" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>';
            heading.innerHTML = '<span style="flex: 1; text-align: center;">' + headingText + '</span>' + iconHTML;

            // Add smooth transition to cards
            cardsContainer.style.transition = 'max-height 0.4s ease, opacity 0.3s ease';
            cardsContainer.style.overflow = 'hidden';
            cardsContainer.style.maxHeight = '0';
            cardsContainer.style.opacity = '0';
            cardsContainer.style.display = 'grid'; // Keep grid display
            cardsContainer.style.pointerEvents = 'none'; // Disable clicks when hidden

            // Track state
            var isOpen = false;

            // Click handler
            heading.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var icon = heading.querySelector('.wsf-subcat-cards-toggle-icon');
                var accordionSection = document.querySelector('.wsf-category-accordion-section');

                if (!isOpen) {
                    // Show cards
                    setTimeout(function() {
                        cardsContainer.style.maxHeight = '3000px';
                        cardsContainer.style.opacity = '1';
                        cardsContainer.style.pointerEvents = 'auto'; // Enable clicks when visible
                    }, 10);
                    icon.style.transform = 'rotate(180deg)';
                    if (accordionSection) {
                        accordionSection.style.marginTop = '0px';
                    }
                    isOpen = true;
                    console.log('WSF: Subcategory cards shown');
                } else {
                    // Hide cards
                    cardsContainer.style.maxHeight = '0';
                    cardsContainer.style.opacity = '0';
                    cardsContainer.style.pointerEvents = 'none'; // Disable clicks when hidden
                    icon.style.transform = 'rotate(0deg)';
                    if (accordionSection) {
                        accordionSection.style.marginTop = '-70px';
                    }
                    isOpen = false;
                    console.log('WSF: Subcategory cards hidden');
                }
            });

            // Mark as initialized
            heading.setAttribute('data-wsf-initialized', 'true');

            console.log('WSF: ✓ Subcategory cards made toggleable');
        }


        // Call this function after page loads
        setTimeout(function() {
            makeSubcategoryCardsToggleable();
        }, 500);

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