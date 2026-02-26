<?php
/**
 * category_display_code.php
 * Add this file to your WordPress plugin or paste into functions.php
 *
 * TWO things this file does:
 *
 * 1. Shortcode [dynamic_product_categories]
 *    → Shows main category cards on any page
 *    → Each card links to its real WooCommerce category URL
 *
 * 2. Hook: woocommerce_before_subcategory_list  (product_cat archive pages)
 *    → On /product-category/vinyl/          → shows subcategory cards
 *    → On /product-category/vinyl/privacy/  → shows sub-subcategory cards (or tags)
 *    → On /product-category/vinyl/privacy/tools-misc/  → shows tag cards
 *    → On above URL + ?tag=top-rail         → shows products with filters
 *
 * Upload location: wp-content/themes/flatsome-child/functions.php  (add_action at bottom)
 * OR as a standalone plugin file.
 */

// ═══════════════════════════════════════════════════════════════════════════════
// SHARED CSS
// ═══════════════════════════════════════════════════════════════════════════════
function dpc_styles() {
    ?>
    <style>
    /* ── Main category grid (shortcode page) ── */
    .main-categories-wrapper { max-width: 1200px; margin: 0 auto; padding: 20px; }
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }
    .category-card {
        background: #fff;
        border: 2px solid #e5e5e5;
        border-radius: 16px;
        padding: 20px 20px 0;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        position: relative;
        margin-bottom: 30px;
        text-decoration: none;
        display: block;
        overflow: visible;
    }
    .category-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        border-color: #327A1F;
    }
    .category-image {
        width: 100%; height: 180px;
        background: #fff;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 15px; border-radius: 8px; overflow: hidden;
    }
    .category-image img { max-width: 90%; max-height: 90%; object-fit: contain; }
    .category-button {
        background: #327A1F; color: white;
        padding: 12px 20px; text-align: center;
        font-weight: 600; font-size: 16px;
        border: none; width: calc(100% - 40px);
        margin: 0 20px; border-radius: 25px;
        position: relative; display: block;
        transform: translateY(50%);
    }
    .category-button span { margin-left: 8px; font-size: 18px; }

    /* ── Category page wrapper ── */
    .dpc-cat-page { max-width: 1200px; margin: 0 auto; padding: 0px 20px; }

    /* ── Breadcrumb ── */
    .dpc-breadcrumb {
        font-size: 13px; color: #888;
        margin-bottom: 24px;
        display: flex; align-items: center; flex-wrap: wrap; gap: 6px;
    }
    .dpc-breadcrumb a { color: #327A1F; text-decoration: none; font-weight: 500; }
    .dpc-breadcrumb a:hover { text-decoration: underline; }
    .dpc-breadcrumb span { color: #ccc; }

    /* ── Page heading ── */
    .dpc-cat-heading {
        font-size: 28px; font-weight: 700; color: #222;
        text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;
    }

    /* ── Category description ── */
    .dpc-cat-desc {
        font-size: 14px; color: #555; line-height: 1.7;
        margin-bottom: 28px; max-width: 800px;
    }
    /* Hide any products/filters that might appear in category description */
    .dpc-cat-desc .dpc-product-card,
    .dpc-cat-desc .dpc-sort-filter-btn-wrapper,
    .dpc-cat-desc .dpc-products-layout,
    .dpc-cat-desc .dpc-products-grid {
        display: none !important;
    }

    /* ── Subcategory / Tag card grid ── */
    .dpc-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 20px 20px;
        row-gap: 50px;
        margin-bottom: 40px;
    }
    .dpc-cat-card {
        background: #fff; border: 2px solid #e5e5e5;
        border-radius: 16px; padding: 30px 16px 40px;
        text-align: center; text-decoration: none;
        display: flex; flex-direction: column; align-items: center;
        position: relative; transition: all 0.25s ease;
        min-height: 160px; overflow: visible;
    }
    .dpc-cat-card:hover {
        border-color: #327A1F; transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .dpc-cat-card .dpc-card-icon {
        width: 90px; height: 90px;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 10px;
    }
    .dpc-cat-card .dpc-card-icon img { max-width: 100%; max-height: 100%; object-fit: contain; }
    .dpc-cat-card .dpc-card-label {
        font-size: 14px; font-weight: 600; color: #333;
        background: #fff; border: 1px solid #e5e5e5;
        border-radius: 20px; padding: 8px 16px;
        position: absolute; bottom: 0; left: 50%;
        transform: translate(-50%, 50%);
        white-space: nowrap; min-width: 110px;
    }
    .dpc-cat-card .dpc-card-count { font-size: 11px; color: #aaa; margin-top: 4px; }

    /* ── Products layout ── */
    .dpc-products-layout { display: grid; grid-template-columns: 240px 1fr; gap: 30px; }

    /* Desktop Left Sidebar */
    .dpc-filters-sidebar {
        background: #f9f9f9;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        padding: 20px;
        align-self: start;
        position: sticky;
        top: 20px;
        max-height: calc(100vh - 40px);
        overflow-y: auto;
    }
    .dpc-filters-sidebar h3 {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        color: #333;
        margin: 0 0 14px;
        padding-bottom: 10px;
        border-bottom: 2px solid #327A1F;
    }
    .dpc-filter-header-desktop {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 2px solid #327A1F;
    }
    .dpc-filter-header-desktop h3 {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        color: #333;
        margin: 0;
        padding: 0;
        border: none;
    }
    .dpc-clear-all {
        font-size: 10px;
        margin-bottom: 0px;
        font-weight: 600;
        color: #327A1F;
        background: none;
        border: none;
        cursor: pointer;
        text-transform: uppercase;
        padding: 6px 12px;
        border-radius: 4px;
        transition: all 0.2s ease;
        display: none;
        white-space: nowrap;
    }
    .dpc-clear-all.show {
        display: block;
    }
    .dpc-clear-all:hover {
        background: #f0f0f0;
    }
    .dpc-filter-group {
        margin-bottom: 16px;
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 12px;
    }
    .dpc-filter-group:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    .dpc-filter-group h4 {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #444;
        margin: 0 0 8px;
    }
    .dpc-filter-group ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .dpc-filter-group ul li {
        margin-bottom: 6px;
    }
    .dpc-filter-group ul li label {
        display: flex !important;
        align-items: center !important;
        gap: 7px;
        font-size: 12px;
        color: #333;
        cursor: pointer;
        line-height: 1.4;
    }
    .dpc-filter-group ul li label input[type="checkbox"] {
        cursor: pointer;
        accent-color: #327A1F;
        width: 13px;
        height: 13px;
        flex-shrink: 0;
        margin: 0 !important;
        padding: 0 !important;
    }
    .dpc-filter-group ul li label .dpc-fc {
        margin-left: auto;
        color: #999;
        font-size: 11px;
    }
    .dpc-filter-group ul li.dpc-hidden {
        display: none !important;
    }

    /* Sort & Filter Button (Mobile Only) */
    .dpc-sort-filter-btn-wrapper {
        display: none;
    }
    .dpc-sort-filter-btn-wrapper::after {
        content: "";
        display: table;
        clear: both;
    }
    .dpc-sort-filter-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fff;
        border: 1px solid #ddd;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        color: #333;
        cursor: pointer;
        transition: all 0.2s ease;
        float: right;
    }
    .dpc-sort-filter-btn:hover {
        background: #f5f5f5;
        border-color: #327A1F;
    }
    .dpc-sort-filter-btn svg {
        width: 16px;
        height: 16px;
    }

    /* Filter Drawer (Mobile Only - Right Side) */
    .dpc-filter-drawer {
        display: none;
    }


    /* Filter Drawer Overlay */
    .dpc-filter-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9998;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .dpc-filter-overlay.show {
        display: block;
        opacity: 1;
    }

    @media (max-width: 768px) {
        .dpc-card-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); }

        /* Hide desktop sidebar and sort bar on mobile */
        .dpc-products-layout { grid-template-columns: 1fr; }
        .dpc-filters-sidebar { display: none !important; }
        .dpc-sort-bar { display: none !important; }

        /* Show Sort & Filter button on mobile */
        .dpc-sort-filter-btn-wrapper {
            display: block;
            max-width: 1200px;
            padding: 0 20px;
            text-align: right;
        }

        /* Show and configure filter drawer on mobile */
        .dpc-filter-drawer {
            display: block;
            position: fixed;
            top: 0;
            right: -100%;
            width: 85%;
            max-width: 320px;
            height: 100%;
            background: white;
            z-index: 9999;
            overflow-y: auto;
            padding: 0;
            transition: right 0.3s ease;
            box-shadow: -4px 0 20px rgba(0,0,0,0.15);
        }
        .dpc-filter-drawer.open {
            right: 0;
        }

        /* Drawer Header */
        .dpc-filter-drawer-header {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            border-bottom: 1px solid #e5e5e5;
            background: #fff;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .dpc-close-drawer {
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            padding: 8px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }
        .dpc-close-drawer:hover {
            background: #f5f5f5;
            color: #333;
        }
        .dpc-close-drawer svg {
            width: 24px;
            height: 24px;
        }

        /* Sort Section in Drawer */
        .dpc-sort-section {
            padding: 20px 24px;
            background: #fff;
        }
        .dpc-sort-section h4 {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #444;
            margin: 0 0 10px;
        }
        .dpc-sort-section select {
            width: 100%;
            padding: 10px 32px 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            background: white;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
        }

        /* Filters Section in Drawer */
        .dpc-filters-section {
            padding: 20px 24px;
        }
        .dpc-filter-header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        .dpc-filter-header-inner h4 {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #444;
            margin: 0;
        }
        .dpc-clear-filters {
            font-size: 11px;
            font-weight: 600;
            color: #327A1F;
            background: none;
            border: none;
            cursor: pointer;
            text-transform: uppercase;
            padding: 6px 12px;
            border-radius: 4px;
            white-space: nowrap;
        }

        /* Filter Groups (Accordion Style for Mobile) */
        .dpc-filter-group-accordion {
            border-bottom: 1px solid #e5e5e5;
        }
        .dpc-filter-group-accordion:last-child {
            border-bottom: none;
        }
        .dpc-filter-group-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            background: none;
            border: none;
            padding: 14px 0;
            cursor: pointer;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #333;
        }
        .dpc-filter-group-title .dpc-accordion-icon {
            width: 16px;
            height: 16px;
            color: #888;
            transition: transform 0.25s ease;
            flex-shrink: 0;
        }
        .dpc-filter-group-accordion.open .dpc-filter-group-title .dpc-accordion-icon {
            transform: rotate(180deg);
            color: #327A1F;
        }
        .dpc-filter-group-title svg {
            width: 16px;
            height: 16px;
        }
        .dpc-filter-group-content {
            display: none;
            padding: 0 0 16px;
        }
        .dpc-filter-group-accordion.open .dpc-filter-group-content {
            display: block;
        }
        .dpc-filter-group-content ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .dpc-filter-group-content ul li {
            margin-bottom: 8px;
        }
        .dpc-filter-group-content ul li label {
            display: flex !important;
            align-items: center !important;
            gap: 8px;
            font-size: 13px;
            color: #333;
            cursor: pointer;
            line-height: 1.4;
        }
        .dpc-filter-group-content ul li label input[type="checkbox"] {
            cursor: pointer;
            accent-color: #327A1F;
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            margin: 0 !important;
            padding: 0 !important;
        }
        .dpc-filter-group-content ul li label .dpc-fc {
            margin-left: auto;
            color: #999;
            font-size: 12px;
        }
        .dpc-filter-group-content ul li.dpc-hidden {
            display: none !important;
        }
    }

    /* ── Products grid ── */
    .dpc-products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 16px; }
    .dpc-product-card {
        border: 1px solid #dde3ec; border-radius: 6px; background: #fff;
        display: flex; flex-direction: column; overflow: hidden;
        transition: box-shadow 0.2s ease;
    }
    .dpc-product-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.12); }
    .dpc-product-card .dpc-img-wrap {
        display: block; background: #f5f7fa; overflow: hidden;
        aspect-ratio: 1/1; text-decoration: none;
    }
    .dpc-product-card .dpc-img-wrap img {
        width: 100%; height: 100%; object-fit: cover; display: block;
        transition: transform 0.3s ease;
    }
    .dpc-product-card:hover .dpc-img-wrap img { transform: scale(1.04); }
    .dpc-product-card .dpc-card-body {
        padding: 10px 12px 12px; display: flex; flex-direction: column; flex-grow: 1;
    }
    .dpc-product-card h4 { font-size: 12px; line-height: 1.45; color: #222; margin: 0 0 8px; flex-grow: 1; }
    .dpc-product-card h4 a { color: #222; text-decoration: none; }
    .dpc-product-card h4 a:hover { color: #327A1F; }
    .dpc-product-card .dpc-price { font-size: 15px; font-weight: 700; color: #327A1F; margin-bottom: 10px; }
    .dpc-add-to-cart-btn {
        display: flex; align-items: center; justify-content: center;
        gap: 6px; width: 100%; background: #327A1F; color: white;
        border: none; padding: 9px 8px; border-radius: 4px;
        font-size: 12px; font-weight: 600; cursor: pointer;
        text-decoration: none; transition: background 0.2s ease; text-align: center;
    }
    .dpc-add-to-cart-btn:hover { background: #2a5330; color: white; }
    .dpc-no-results { grid-column: 1/-1; text-align: center; padding: 60px 20px; color: #888; font-size: 15px; }

    /* Desktop Sort Bar */
    .dpc-sort-bar {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        margin-bottom: 14px;
        gap: 10px;
        font-size: 13px;
        color: #555;
    }
    .dpc-sort-bar select {
        padding: 5px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 13px;
        cursor: pointer;
    }

    /* Hide theme/WooCommerce breadcrumbs on category pages */
    .tax-product_cat .breadcrumbs,
    .tax-product_cat .woocommerce-breadcrumb,
    .tax-product_cat nav.woocommerce-breadcrumb,
    .tax-product_cat .flatsome-breadcrumbs { display: none !important; }

    /* Hide WooCommerce default sorting dropdown and filter buttons */
    .woocommerce-ordering,
    .woocommerce .woocommerce-ordering,
    .woocommerce-result-count,
    .tax-product_cat .shop-page-title .woocommerce-ordering,
    .tax-product_cat .filter-button,
    .tax-product_cat button.filter-button { display: none !important; }

    /* Hide GET QUOTE button from top header on category pages (not from mobile left menu) */
    .tax-product_cat .header-wrapper .header-button:not(.off-canvas .header-button),
    .tax-product_cat .header-top .header-button,
    .tax-product_cat .top-bar-nav .header-button { display: none !important; }
    </style>
    <?php
}

// ═══════════════════════════════════════════════════════════════════════════════
// SHORTCODE: [dynamic_product_categories]
// Shows main category cards — each links to its real category URL
// ═══════════════════════════════════════════════════════════════════════════════
add_shortcode('dynamic_product_categories', 'display_dynamic_product_categories');
function display_dynamic_product_categories() {
    ob_start();

    dpc_styles();

    $main_category_slugs = array('vinyl', 'ornamental', 'chain-link', 'simtek', 'modern', 'trex', 'wood', 'agriculture', 'metal-horse-fence');

    $parent_cats = get_terms(array(
        'taxonomy'   => 'product_cat',
        'slug'       => $main_category_slugs,
        'hide_empty' => true,
    ));

    $category_images = array(
        'chain-link'        => 'https://staging2.wholesalefencing.com/wp-content/uploads/2023/05/chainlink-fence-with-vinyl-slats-150x150.png',
        'modern'            => 'https://wholesalefencing.com/wp-content/uploads/2025/01/Privacy.webp',
        'vinyl'             => 'https://wholesalefencing.com/wp-content/uploads/2022/10/white-2.png',
        'simtek'            => 'https://wholesalefencing.com/wp-content/uploads/2022/10/EcoStone-6x6Panel-beige-wholesale-viinyl-fencing-1.jpg',
        'wood'              => 'https://staging2.wholesalefencing.com/wp-content/uploads/2023/05/ashland-simulated-wood-grain-150x150.jpeg',
        'agriculture'       => 'https://wholesalefencing.com/wp-content/uploads/2023/03/H2bb27b9f5b1940eea84dadc664bdd640u.png',
        'trex'              => 'https://wholesalefencing.com/wp-content/uploads/2022/10/IMG_1911-scaled.jpg',
        'ornamental'        => 'https://wholesalefencing.com/wp-content/uploads/2023/03/H2bb27b9f5b1940eea84dadc664bdd640u.png',
        'metal-horse-fence' => 'https://wholesalefencing.com/wp-content/uploads/2023/03/H2bb27b9f5b1940eea84dadc664bdd640u.png',
    );

    // Sort by the order in $main_category_slugs
    $ordered = array();
    foreach ($main_category_slugs as $slug) {
        foreach ($parent_cats as $cat) {
            if ($cat->slug === $slug) { $ordered[] = $cat; break; }
        }
    }
    ?>
    <div class="main-categories-wrapper">
        <div class="category-grid">
            <?php foreach ($ordered as $cat):
                $thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
                $image_url    = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
                if (!$image_url && isset($category_images[$cat->slug])) {
                    $image_url = $category_images[$cat->slug];
                }
            ?>
            <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="category-card">
                <div class="category-image">
                    <?php if ($image_url): ?>
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($cat->name); ?>">
                    <?php else: ?>
                        <div style="font-size:80px;"></div>
                    <?php endif; ?>
                </div>
                <div class="category-button">
                    <?php echo esc_html($cat->name); ?> <span>›</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// ═══════════════════════════════════════════════════════════════════════════════
// CATEGORY PAGE OVERRIDE
// Uses template_redirect to render a fully custom page (bypasses WooCommerce entirely)
// ═══════════════════════════════════════════════════════════════════════════════
add_action('template_redirect', 'dpc_override_category_page');
function dpc_override_category_page() {
    if (!is_tax('product_cat')) return;

    // Hide theme breadcrumb (Flatsome)
    add_filter('flatsome_breadcrumbs', '__return_false');
    remove_action('flatsome_breadcrumbs', 'flatsome_breadcrumb', 10);
    add_filter('woocommerce_breadcrumb_defaults', function($defaults) {
        $defaults['wrap_before'] = '<nav class="dpc-hidden-bc" style="display:none">';
        return $defaults;
    });

    // Render full page ourselves: get_header() + our content + get_footer()
    // This completely bypasses WooCommerce template + sidebar
    get_header();
    dpc_render_category_page();
    get_footer();
    exit;
}

function dpc_render_category_page() {
    $current_cat = get_queried_object();
    if (!$current_cat || !isset($current_cat->term_id)) return;

    $cat_id   = $current_cat->term_id;
    $cat_name = $current_cat->name;

    echo '<div id="primary" class="content-area" style="width:100%;max-width:100%;float:none;margin:0 auto;">';
    echo '<main id="main" class="site-main" role="main">';

    // Child categories
    $child_cats = get_terms(array(
        'taxonomy'   => 'product_cat',
        'parent'     => $cat_id,
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ));
    $has_children = !empty($child_cats) && !is_wp_error($child_cats);

    // Ancestors for breadcrumb
    $ancestors = array_reverse(get_ancestors($cat_id, 'product_cat'));

    dpc_styles();
    ?>
    <div class="dpc-cat-page">

        <!-- Breadcrumb -->
        <nav class="dpc-breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
            <?php foreach ($ancestors as $anc_id):
                $anc = get_term($anc_id, 'product_cat');
                if (!is_wp_error($anc)):
            ?>
                <span>›</span>
                <a href="<?php echo esc_url(get_term_link($anc)); ?>"><?php echo esc_html($anc->name); ?></a>
            <?php endif; endforeach; ?>
            <span>›</span>
            <strong><?php echo esc_html($cat_name); ?></strong>
        </nav>

        <h1 class="dpc-cat-heading"><?php echo esc_html($cat_name); ?></h1>

        <?php if ($current_cat->description): ?>
            <div class="dpc-cat-desc"><?php echo wp_kses_post($current_cat->description); ?></div>
        <?php endif; ?>

        <?php if ($has_children): ?>
            <!-- ── Has subcategories: show category cards ── -->
            <div class="dpc-card-grid">
                <?php foreach ($child_cats as $child):
                    $thumb_id  = get_term_meta($child->term_id, 'thumbnail_id', true);
                    $thumb_url = $thumb_id ? wp_get_attachment_url($thumb_id) : '';
                    $count     = dpc_count_products($child->term_id);
                ?>
                <a href="<?php echo esc_url(get_term_link($child)); ?>" class="dpc-cat-card">
                    <div class="dpc-card-icon">
                        <?php if ($thumb_url): ?>
                            <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($child->name); ?>">
                        <?php else: ?>
                            <span style="font-size:48px;"></span>
                        <?php endif; ?>
                    </div>
                    <div class="dpc-card-label"><?php echo esc_html($child->name); ?></div>
                </a>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <!-- ── No subcategories: show products directly ── -->
            <?php dpc_render_products($cat_id); ?>

        <?php endif; ?>

    </div><!-- .dpc-cat-page -->

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.dpc-filter-cb').forEach(function(cb) {
            cb.addEventListener('change', dpcApplyFilters);
        });
        var clearBtn = document.getElementById('dpc-clear-btn');
        if (clearBtn) clearBtn.addEventListener('click', dpcClearFilters);
    });
    function dpcApplyFilters() {
        var active = {};
        document.querySelectorAll('.dpc-filter-cb:checked').forEach(function(cb) {
            var attr = cb.dataset.attr, val = cb.dataset.val;
            if (!active[attr]) active[attr] = [];
            active[attr].push(val.toLowerCase());
        });

        // Show/hide Clear All button
        var clearBtn = document.getElementById('dpc-clear-btn');
        if (clearBtn) {
            if (Object.keys(active).length > 0) {
                clearBtn.classList.add('show');
            } else {
                clearBtn.classList.remove('show');
            }
        }

        // Filter products
        var visibleCards = [];
        document.querySelectorAll('.dpc-product-card').forEach(function(card) {
            if (!Object.keys(active).length) {
                card.style.display = '';
                visibleCards.push(card);
                return;
            }
            var attrs = JSON.parse(card.dataset.attrs || '{}'), show = true;
            for (var attr in active) {
                var cv = (attrs[attr] || '').toLowerCase();
                if (!active[attr].some(function(v){ return cv.indexOf(v) !== -1; })) { show = false; break; }
            }
            card.style.display = show ? '' : 'none';
            if (show) visibleCards.push(card);
        });

        // Update "No results" message
        var grid = document.querySelector('.dpc-products-grid');
        if (grid) {
            var nr = grid.querySelector('.dpc-no-results');
            if (nr) nr.style.display = visibleCards.length === 0 ? '' : 'none';
        }

        // Dynamic filter update: count available options and update counts
        var availableAttrs = {};
        visibleCards.forEach(function(card) {
            var attrs = JSON.parse(card.dataset.attrs || '{}');
            for (var attr in attrs) {
                if (!availableAttrs[attr]) availableAttrs[attr] = {};
                var vals = attrs[attr].split(',').map(function(v){ return v.trim().toLowerCase(); });
                vals.forEach(function(v){
                    if (v) {
                        if (!availableAttrs[attr][v]) availableAttrs[attr][v] = 0;
                        availableAttrs[attr][v]++;
                    }
                });
            }
        });

        document.querySelectorAll('.dpc-filter-cb').forEach(function(cb) {
            var attr = cb.dataset.attr, val = cb.dataset.val.toLowerCase();
            var li = cb.closest('li');
            if (!li) return;

            var countSpan = li.querySelector('.dpc-fc');
            var count = 0;

            // If this filter is checked, always show it
            if (cb.checked) {
                li.classList.remove('dpc-hidden');
                if (countSpan && availableAttrs[attr] && availableAttrs[attr][val]) {
                    countSpan.textContent = '(' + availableAttrs[attr][val] + ')';
                }
                return;
            }

            // If no filters active, show all with original counts (restore from data attribute)
            if (!Object.keys(active).length) {
                li.classList.remove('dpc-hidden');
                if (countSpan) {
                    var originalCount = li.dataset.originalCount;
                    if (originalCount) countSpan.textContent = '(' + originalCount + ')';
                }
                return;
            }

            // Check if this option is available in currently visible products
            if (availableAttrs[attr] && availableAttrs[attr][val]) {
                li.classList.remove('dpc-hidden');
                count = availableAttrs[attr][val];
                if (countSpan) countSpan.textContent = '(' + count + ')';
            } else {
                li.classList.add('dpc-hidden');
            }
        });
    }
    function dpcClearFilters() {
        document.querySelectorAll('.dpc-filter-cb:checked').forEach(function(cb){ cb.checked = false; });
        dpcApplyFilters();
    }
    function dpcToggleFilterDrawer() {
        var drawer = document.getElementById('dpc-filter-drawer');
        var overlay = document.getElementById('dpc-filter-overlay');

        if (drawer && overlay) {
            var isOpen = drawer.classList.contains('open');

            if (isOpen) {
                // Close drawer
                drawer.classList.remove('open');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            } else {
                // Open drawer
                drawer.classList.add('open');
                overlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }
    }
    function dpcToggleAccordion(btn) {
        var accordion = btn.closest('.dpc-filter-group-accordion');
        if (accordion) {
            accordion.classList.toggle('open');
        }
    }
    function dpcHandleSort(val) {
        dpcSort(val);
    }
    function dpcSort(val) {
        var grid = document.getElementById('dpc-grid');
        if (!grid) return;
        var cards = Array.from(grid.querySelectorAll('.dpc-product-card'));
        cards.sort(function(a, b) {
            if (val === 'title_asc') return a.dataset.title.localeCompare(b.dataset.title);
            if (val === 'price_asc' || val === 'price_desc') {
                var pa = parseFloat(a.dataset.price.replace(/[^0-9.]/g,''))||0;
                var pb = parseFloat(b.dataset.price.replace(/[^0-9.]/g,''))||0;
                return val === 'price_asc' ? pa-pb : pb-pa;
            }
            return 0;
        });
        cards.forEach(function(c){ grid.appendChild(c); });
    }
    </script>
    <?php
    echo '</main></div>'; // close #main and #primary
}

// ═══════════════════════════════════════════════════════════════════════════════
// HELPERS
// ═══════════════════════════════════════════════════════════════════════════════

function dpc_count_products($term_id) {
    $ids      = array($term_id);
    $children = get_term_children($term_id, 'product_cat');
    if (!is_wp_error($children)) $ids = array_merge($ids, $children);
    $q = new WP_Query(array(
        'post_type' => 'product', 'posts_per_page' => -1, 'fields' => 'ids',
        'tax_query' => array(array('taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $ids)),
    ));
    return $q->found_posts;
}

// ═══════════════════════════════════════════════════════════════════════════════
// SINGLE PRODUCT PAGE OVERRIDE
// Applies only to products that have NO Flatsome page builder content
// (i.e. newly imported products via CSV)
// ═══════════════════════════════════════════════════════════════════════════════
add_action('template_redirect', 'dpc_single_product_override');
function dpc_single_product_override() {
    if (!is_singular('product')) return;

    global $post;
    // If product has Flatsome page builder content, let it render normally
    $content = $post->post_content;
    if (strpos($content, '[ux_') !== false || strpos($content, 'flatsome-ux') !== false) return;

    get_header();
    dpc_render_single_product();
    get_footer();
    exit;
}

function dpc_render_single_product() {
    global $post;
    $product = wc_get_product($post->ID);
    if (!$product) return;

    $title       = get_the_title($post->ID);
    $price_html  = $product->get_price_html();
    $description = $product->get_description();
    $short_desc  = $product->get_short_description();
    $sku         = $product->get_sku();
    $permalink   = get_permalink($post->ID);

    // Gallery images
    $gallery_ids    = $product->get_gallery_image_ids();
    $main_image_id  = $product->get_image_id();
    $all_image_ids  = $main_image_id ? array_merge([$main_image_id], $gallery_ids) : $gallery_ids;

    // Attributes
    $attributes = $product->get_attributes();

    // Breadcrumb ancestors
    $terms     = get_the_terms($post->ID, 'product_cat');
    $cat       = null;
    $ancestors = [];
    if ($terms && !is_wp_error($terms)) {
        foreach ($terms as $t) {
            if (!$t->parent) continue;
            $cat = $t; break;
        }
        if (!$cat) $cat = $terms[0];
        $anc_ids   = array_reverse(get_ancestors($cat->term_id, 'product_cat'));
        foreach ($anc_ids as $aid) {
            $anc = get_term($aid, 'product_cat');
            if (!is_wp_error($anc)) $ancestors[] = $anc;
        }
        $ancestors[] = $cat;
    }
    ?>
    <style>
    .dpc-sp-page { max-width: 1200px; margin: 0 auto; padding: 30px 20px; font-family: Montserrat, sans-serif; }
    .dpc-sp-breadcrumb { font-size: 13px; color: #888; margin-bottom: 24px; display: flex; align-items: center; flex-wrap: wrap; gap: 6px; }
    .dpc-sp-breadcrumb a { color: #327A1F; text-decoration: none; font-weight: 500; }
    .dpc-sp-breadcrumb a:hover { text-decoration: underline; }
    .dpc-sp-breadcrumb span { color: #ccc; }
    .dpc-sp-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: start; }
    @media (max-width: 768px) { .dpc-sp-layout { grid-template-columns: 1fr; gap: 24px; } }

    /* Gallery */
    .dpc-sp-gallery { position: sticky; top: 20px; }
    .dpc-sp-main-img {
        width: 100%; aspect-ratio: 1/1; background: #f5f7fa;
        border: 1px solid #e5e5e5; border-radius: 10px; overflow: hidden;
        display: flex; align-items: center; justify-content: center; margin-bottom: 12px;
    }
    .dpc-sp-main-img img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .dpc-sp-thumbs { display: flex; gap: 8px; flex-wrap: wrap; }
    .dpc-sp-thumb {
        width: 70px; height: 70px; border: 2px solid #e5e5e5; border-radius: 6px;
        overflow: hidden; cursor: pointer; background: #f5f7fa;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .dpc-sp-thumb:hover, .dpc-sp-thumb.active { border-color: #327A1F; }
    .dpc-sp-thumb img { max-width: 100%; max-height: 100%; object-fit: contain; }
    .dpc-sp-no-img { font-size: 80px; }

    /* Info */
    .dpc-sp-info {}
    .dpc-sp-sku { font-size: 12px; color: #999; margin-bottom: 8px; }
    .dpc-sp-title { font-size: 22px; font-weight: 700; color: #212121; line-height: 1.35; margin: 0 0 16px; }
    .dpc-sp-price { font-size: 28px; font-weight: 700; color: #327A1F; margin-bottom: 16px; }
    .dpc-sp-short-desc { font-size: 14px; color: #444; line-height: 1.6; margin-bottom: 20px; }
    .dpc-sp-atc-btn {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        background: #244e5a; color: #fff; border: none; padding: 14px 28px;
        border-radius: 50px; font-size: 14px; font-weight: 700; cursor: pointer;
        text-decoration: none; width: 100%; transition: background 0.2s ease;
        margin-bottom: 24px; letter-spacing: 0.5px; text-transform: uppercase;
        box-sizing: border-box;
    }
    .dpc-sp-atc-btn:hover { background: #1a3a44; color: #fff; }

    /* Description */
    .dpc-sp-desc-heading { font-size: 14px; font-weight: 700; text-transform: uppercase; color: #244e5a; border-bottom: 2px solid #327A1F; padding-bottom: 8px; margin: 0 0 14px; }
    .dpc-sp-desc { font-size: 14px; color: #444; line-height: 1.7; }

    /* Accordion */
    .dpc-sp-accordion { margin-top: 50px; }
    .dpc-sp-acc-item {
        border-bottom: 1px solid #e0e0e0;
    }
    .dpc-sp-acc-title {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 4px; cursor: pointer; user-select: none;
        font-size: 14px; font-weight: 700; color: #333; text-transform: uppercase;
        letter-spacing: 0.5px; background: none; border: none; width: 100%;
        text-align: left;
    }
    .dpc-sp-acc-title:hover { color: #244e5a; }
    .dpc-sp-acc-title .dpc-acc-icon {
        font-size: 18px; color: #888; transition: transform 0.25s ease; line-height: 1;
    }
    .dpc-sp-acc-item.open .dpc-sp-acc-title { color: #244e5a; }
    .dpc-sp-acc-item.open .dpc-acc-icon { transform: rotate(180deg); color: #327A1F; }
    .dpc-sp-acc-inner {
        display: none; padding: 16px 4px 24px; font-size: 14px; color: #444; line-height: 1.7;
    }
    .dpc-sp-acc-inner table { width: 100%; border-collapse: collapse; }
    .dpc-sp-acc-inner table th {
        background: #f5f7fa; font-size: 11px; font-weight: 700; text-transform: uppercase;
        color: #555; padding: 10px 14px; text-align: left; border-bottom: 1px solid #e5e5e5; width: 35%;
    }
    .dpc-sp-acc-inner table td {
        font-size: 13px; color: #333; padding: 10px 14px; border-bottom: 1px solid #f0f0f0;
    }
    .dpc-sp-acc-inner table tr:last-child th,
    .dpc-sp-acc-inner table tr:last-child td { border-bottom: none; }

    /* View More Parts */
    .dpc-vmp-wrap { margin-top: 0; padding-top: 0; }
    .dpc-vmp-toggle {
        display: flex; align-items: center; justify-content: space-between;
        width: 100%; background: none; border: none; cursor: pointer;
        font-size: 14px; font-weight: 700; color: #333; text-transform: uppercase;
        letter-spacing: 0.5px; padding: 0 0 16px; text-align: left;
    }
    .dpc-vmp-toggle:hover { color: #244e5a; }
    .dpc-vmp-icon { font-size: 18px; color: #888; transition: transform 0.25s ease; }
    .dpc-vmp-wrap.open .dpc-vmp-toggle { color: #244e5a; }
    .dpc-vmp-wrap.open .dpc-vmp-icon { transform: rotate(180deg); color: #327A1F; }
    .dpc-vmp-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 12px;
        padding-bottom: 10px;
    }
    .dpc-vmp-card {
        display: flex; flex-direction: column; align-items: center;
        text-decoration: none; border: 1px solid #e5e5e5; border-radius: 10px;
        padding: 12px 8px 10px; background: #fff; text-align: center;
        transition: all 0.2s ease;
    }
    .dpc-vmp-card:hover { border-color: #327A1F; box-shadow: 0 3px 10px rgba(0,0,0,0.08); transform: translateY(-2px); }
    .dpc-vmp-img { width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px; }
    .dpc-vmp-img img { max-width: 100%; max-height: 100%; object-fit: contain; }
    .dpc-vmp-label { font-size: 11px; font-weight: 600; color: #333; line-height: 1.3; }

    /* Hide theme breadcrumb */
    .single-product .breadcrumbs,
    .single-product .woocommerce-breadcrumb,
    .single-product nav.woocommerce-breadcrumb { display: none !important; }
    </style>

    <div id="primary" class="content-area" style="width:100%;max-width:100%;float:none;margin:0 auto;">
    <main id="main" class="site-main" role="main">
    <div class="dpc-sp-page">

        <!-- Breadcrumb -->
        <nav class="dpc-sp-breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
            <?php foreach ($ancestors as $anc): ?>
                <span>›</span>
                <a href="<?php echo esc_url(get_term_link($anc)); ?>"><?php echo esc_html($anc->name); ?></a>
            <?php endforeach; ?>
            <span>›</span>
            <strong><?php echo esc_html($title); ?></strong>
        </nav>

        <div class="dpc-sp-layout">

            <!-- LEFT: Gallery -->
            <div class="dpc-sp-gallery">
                <div class="dpc-sp-main-img" id="dpc-main-img">
                    <?php if ($all_image_ids): ?>
                        <img id="dpc-main-img-tag" src="<?php echo esc_url(wp_get_attachment_url($all_image_ids[0])); ?>" alt="<?php echo esc_attr($title); ?>">
                    <?php else: ?>
                        <div class="dpc-sp-no-img"></div>
                    <?php endif; ?>
                </div>
                <?php if (count($all_image_ids) > 1): ?>
                <div class="dpc-sp-thumbs">
                    <?php foreach ($all_image_ids as $i => $img_id):
                        $img_url = wp_get_attachment_url($img_id);
                    ?>
                    <div class="dpc-sp-thumb <?php echo $i === 0 ? 'active' : ''; ?>" onclick="dpcSetImg('<?php echo esc_js($img_url); ?>', this)">
                        <img src="<?php echo esc_url($img_url); ?>" alt="">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- RIGHT: Info -->
            <div class="dpc-sp-info">
                <?php if ($sku): ?>
                    <div class="dpc-sp-sku">SKU: <?php echo esc_html($sku); ?></div>
                <?php endif; ?>

                <h1 class="dpc-sp-title"><?php echo esc_html($title); ?></h1>

                <div class="dpc-sp-price"><?php echo wp_kses_post($price_html); ?></div>

                <?php if ($short_desc): ?>
                    <div class="dpc-sp-short-desc"><?php echo wp_kses_post($short_desc); ?></div>
                <?php endif; ?>

                <!-- Add to Cart -->
                <form class="cart" action="<?php echo esc_url($permalink); ?>" method="post" enctype="multipart/form-data">
                    <div style="display:flex;align-items:stretch;gap:12px;margin-bottom:16px;">
                        <input type="number" name="quantity" value="1" min="1"
                               style="width:70px;height:44px;padding:0 10px;border:1px solid #ccc;border-radius:50px;font-size:15px;text-align:center;box-sizing:border-box;">
                        <button type="submit" name="add-to-cart" value="<?php echo esc_attr($post->ID); ?>" class="dpc-sp-atc-btn" style="margin-bottom:0;flex:1;height:44px;padding:0 28px;box-sizing:border-box;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                            </svg>
                            Add to Cart
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- Tabs: Overview / Specifications / Videos / Reviews -->
        <?php
        // Collect tab data
        $tab_overview = $description ?: $short_desc;
        $tab_specs     = $attributes;  // already fetched above

        // Check for video URL in product meta (_video_url) or attribute named 'video'
        $video_url = get_post_meta($post->ID, '_video_url', true);
        if (!$video_url) $video_url = get_post_meta($post->ID, 'video_url', true);
        if (!$video_url) {
            // Try attribute named "video" or "video url"
            foreach ($attributes as $attr_slug => $attr_obj) {
                if (strpos(strtolower($attr_slug), 'video') !== false) {
                    if (is_object($attr_obj)) {
                        if ($attr_obj->is_taxonomy()) {
                            $vterms = wp_get_post_terms($post->ID, $attr_slug, ['fields' => 'names']);
                            if (!is_wp_error($vterms) && !empty($vterms)) $video_url = $vterms[0];
                        } else {
                            $opts = $attr_obj->get_options();
                            if (!empty($opts)) $video_url = $opts[0];
                        }
                    }
                    break;
                }
            }
        }

        // Build tab list (only show tabs with content)
        $tabs = [];
        if ($tab_overview)       $tabs[] = 'overview';
        if (!empty($tab_specs))  $tabs[] = 'specifications';
        if ($video_url)          $tabs[] = 'videos';
        $tabs[] = 'reviews';  // Always show reviews tab (WooCommerce handles content)
        ?>

        <?php if (!empty($tabs)): ?>
        <div class="dpc-sp-accordion">

            <!-- Overview Accordion -->
            <?php if (in_array('overview', $tabs)): ?>
            <div class="dpc-sp-acc-item">
                <button class="dpc-sp-acc-title" onclick="dpcAccToggle(this)">
                    <span>Overview</span>
                    <span class="dpc-acc-icon">&#8964;</span>
                </button>
                <div class="dpc-sp-acc-inner">
                    <?php echo wp_kses_post($tab_overview); ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Specifications Accordion -->
            <?php if (in_array('specifications', $tabs)): ?>
            <div class="dpc-sp-acc-item">
                <button class="dpc-sp-acc-title" onclick="dpcAccToggle(this)">
                    <span>Specifications</span>
                    <span class="dpc-acc-icon">&#8964;</span>
                </button>
                <div class="dpc-sp-acc-inner">
                    <table>
                        <?php foreach ($tab_specs as $attr_slug => $attr_obj):
                            $label = wc_attribute_label($attr_slug);
                            $label_lower = strtolower($label);

                            // Skip Brand and Post Size from specifications
                            if ($label_lower === 'brand' || $label_lower === 'post size' || $label_lower === 'post-size') continue;

                            $value = '';
                            if (is_object($attr_obj) && method_exists($attr_obj, 'is_taxonomy')) {
                                if ($attr_obj->is_taxonomy()) {
                                    $spec_terms = wp_get_post_terms($post->ID, $attr_slug, ['fields' => 'names']);
                                    $value = (!is_wp_error($spec_terms) && is_array($spec_terms)) ? implode(', ', $spec_terms) : '';
                                } else {
                                    $opts  = $attr_obj->get_options();
                                    $value = is_array($opts) ? implode(', ', $opts) : '';
                                }
                            }
                            if (!$value) continue;
                        ?>
                        <tr>
                            <th><?php echo esc_html($label); ?></th>
                            <td><?php echo esc_html($value); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Videos Accordion -->
            <?php if (in_array('videos', $tabs)): ?>
            <div class="dpc-sp-acc-item">
                <button class="dpc-sp-acc-title" onclick="dpcAccToggle(this)">
                    <span>Videos</span>
                    <span class="dpc-acc-icon">&#8964;</span>
                </button>
                <div class="dpc-sp-acc-inner">
                    <?php
                    $embed = '';
                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $video_url, $m)) {
                        $embed = 'https://www.youtube.com/embed/' . $m[1];
                    } elseif (preg_match('/vimeo\.com\/(\d+)/', $video_url, $m)) {
                        $embed = 'https://player.vimeo.com/video/' . $m[1];
                    }
                    if ($embed): ?>
                        <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:8px;">
                            <iframe src="<?php echo esc_url($embed); ?>" style="position:absolute;top:0;left:0;width:100%;height:100%;border:none;" allowfullscreen loading="lazy"></iframe>
                        </div>
                    <?php else: ?>
                        <p><a href="<?php echo esc_url($video_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($video_url); ?></a></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Reviews Accordion -->
            <div class="dpc-sp-acc-item">
                <button class="dpc-sp-acc-title" onclick="dpcAccToggle(this)">
                    <span>Reviews</span>
                    <span class="dpc-acc-icon">&#8964;</span>
                </button>
                <div class="dpc-sp-acc-inner">
                    <?php
                    if (comments_open($post->ID)) {
                        comment_form([], $post->ID);
                        $comments = get_comments(['post_id' => $post->ID, 'status' => 'approve', 'type' => 'review']);
                        if ($comments) {
                            echo '<div style="margin-top:30px;">';
                            wp_list_comments(['type' => 'review', 'callback' => null], $comments);
                            echo '</div>';
                        }
                    } else {
                        echo '<p style="color:#888;">Reviews are closed for this product.</p>';
                    }
                    ?>
                </div>
            </div>

        </div><!-- .dpc-sp-accordion -->
        <?php endif; ?>

        <?php
        // ── View More Parts: sibling sub-categories from the same main (level-1) category ──
        // Find the top-level (level 1) ancestor of this product's category
        $sp_terms   = get_the_terms($post->ID, 'product_cat');
        $sp_top_cat = null;
        if ($sp_terms && !is_wp_error($sp_terms)) {
            foreach ($sp_terms as $sp_t) {
                $sp_ancs = get_ancestors($sp_t->term_id, 'product_cat');
                if (!empty($sp_ancs)) {
                    // deepest ancestor = top-level
                    $sp_top_cat = get_term(end($sp_ancs), 'product_cat');
                    break;
                } elseif (!$sp_t->parent) {
                    $sp_top_cat = $sp_t;
                    break;
                }
            }
        }

        // Get level-2 children of that top-level category
        $sp_siblings = [];
        if ($sp_top_cat && !is_wp_error($sp_top_cat)) {
            $sp_siblings = get_terms([
                'taxonomy'   => 'product_cat',
                'parent'     => $sp_top_cat->term_id,
                'hide_empty' => true,
                'orderby'    => 'name',
                'order'      => 'ASC',
            ]);
        }

        if (!empty($sp_siblings) && !is_wp_error($sp_siblings)):
        ?>
        <div class="dpc-vmp-wrap">
            <button class="dpc-vmp-toggle" onclick="dpcVmpToggle(this)">
                <span>View More Parts in <?php echo esc_html($sp_top_cat->name); ?></span>
                <span class="dpc-vmp-icon">&#8964;</span>
            </button>
            <div class="dpc-vmp-grid" id="dpc-vmp-grid" style="display:none;">
                <?php foreach ($sp_siblings as $sp_sib):
                    $sib_thumb_id  = get_term_meta($sp_sib->term_id, 'thumbnail_id', true);
                    $sib_thumb_url = $sib_thumb_id ? wp_get_attachment_url($sib_thumb_id) : '';
                ?>
                <a href="<?php echo esc_url(get_term_link($sp_sib)); ?>" class="dpc-vmp-card">
                    <div class="dpc-vmp-img">
                        <?php if ($sib_thumb_url): ?>
                            <img src="<?php echo esc_url($sib_thumb_url); ?>" alt="<?php echo esc_attr($sp_sib->name); ?>">
                        <?php else: ?>
                            <span style="font-size:32px;"></span>
                        <?php endif; ?>
                    </div>
                    <div class="dpc-vmp-label"><?php echo esc_html($sp_sib->name); ?></div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- .dpc-sp-page -->

    <script>
    function dpcSetImg(url, el) {
        document.getElementById('dpc-main-img-tag').src = url;
        document.querySelectorAll('.dpc-sp-thumb').forEach(function(t){ t.classList.remove('active'); });
        el.classList.add('active');
    }
    function dpcVmpToggle(btn) {
        var wrap  = btn.closest('.dpc-vmp-wrap');
        var grid  = document.getElementById('dpc-vmp-grid');
        var isOpen = wrap.classList.contains('open');
        if (isOpen) {
            wrap.classList.remove('open');
            grid.style.display = 'none';
        } else {
            wrap.classList.add('open');
            grid.style.display = 'grid';
        }
    }
    function dpcAccToggle(btn) {
        var item = btn.closest('.dpc-sp-acc-item');
        var inner = item.querySelector('.dpc-sp-acc-inner');
        var isOpen = item.classList.contains('open');
        if (isOpen) {
            item.classList.remove('open');
            inner.style.display = 'none';
        } else {
            item.classList.add('open');
            inner.style.display = 'block';
        }
    }
    </script>

    </main></div>
    <?php
}

// ═══════════════════════════════════════════════════════════════════════════════
// CONTINUE SHOPPING URL → redirect to /products/ instead of /shop/
// ═══════════════════════════════════════════════════════════════════════════════
add_filter('woocommerce_continue_shopping_redirect', function() {
    return home_url('/products/');
});

function dpc_render_products($cat_id) {
    $query = new WP_Query(array(
        'post_type'      => 'product',
        'posts_per_page' => 500,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'tax_query'      => array(array(
            'taxonomy'         => 'product_cat',
            'field'            => 'term_id',
            'terms'            => $cat_id,
            'include_children' => true,
        )),
    ));

    $attr_data = array(); $all_products = array();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $pid = get_the_ID(); $product = wc_get_product($pid);
            if (!$product) continue;
            $attrs_for_card = array();
            foreach ($product->get_attributes() as $attr_slug => $attr_obj) {
                $label = wc_attribute_label($attr_slug); $value = '';
                if (is_object($attr_obj) && method_exists($attr_obj, 'is_taxonomy')) {
                    if ($attr_obj->is_taxonomy()) {
                        $terms = wp_get_post_terms($pid, $attr_slug, array('fields' => 'names'));
                        $value = (!is_wp_error($terms) && is_array($terms)) ? implode(', ', $terms) : '';
                    } else {
                        $opts = $attr_obj->get_options();
                        $value = is_array($opts) ? implode(', ', $opts) : '';
                    }
                }
                if ($value === '') continue;
                $attrs_for_card[$label] = $value;
                foreach (array_map('trim', explode(',', $value)) as $v) {
                    if ($v === '') continue;
                    if (!isset($attr_data[$label][$v])) $attr_data[$label][$v] = 0;
                    $attr_data[$label][$v]++;
                }
            }
            $all_products[] = array(
                'id'          => $pid,
                'title'       => get_the_title(),
                'permalink'   => get_permalink(),
                'add_to_cart' => esc_url(wc_get_cart_url() . '?add-to-cart=' . $pid),
                'price_html'  => $product->get_price_html(),
                'thumbnail'   => get_the_post_thumbnail($pid, array(300, 300)),
                'attrs'       => $attrs_for_card,
            );
        }
        wp_reset_postdata();
    }
    // Custom order for filters - specific attributes first, then rest alphabetically
    $priority_order = array(
        'product part'        => 1,
        'product-part'        => 1,
        'color'               => 2,
        'height'              => 3,
        'width'               => 4,
        'product diameter'    => 5,
        'product-diameter'    => 5,
        'length'              => 6,
        'product thickness'   => 7,
        'product-thickness'   => 7,
        'finish style'        => 8,
        'finish-style'        => 8,
    );

    uksort($attr_data, function($a, $b) use ($priority_order) {
        $a_lower = strtolower($a);
        $b_lower = strtolower($b);
        $a_priority = isset($priority_order[$a_lower]) ? $priority_order[$a_lower] : 999;
        $b_priority = isset($priority_order[$b_lower]) ? $priority_order[$b_lower] : 999;

        if ($a_priority !== $b_priority) {
            return $a_priority - $b_priority;
        }
        return strcmp($a, $b); // Alphabetical for rest
    });

    foreach ($attr_data as &$vals) ksort($vals);
    unset($vals);
    ?>
    <!-- Sort & Filter Button (Mobile Only) -->
    <div class="dpc-sort-filter-btn-wrapper">
        <button class="dpc-sort-filter-btn" onclick="dpcToggleFilterDrawer()">
            <span>Sort & Filter</span>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                <path d="M0 3h7v2H0V3zm0 4h5v2H0V7zm0 4h8v2H0v-2zm10-8h6v2h-6V3zm0 8h6v2h-6v-2zm-2-4h8v2H8V7z"/>
            </svg>
        </button>
    </div>

    <!-- Mobile Filter Drawer (Right Side) -->
    <aside id="dpc-filter-drawer" class="dpc-filter-drawer">
        <div class="dpc-filter-drawer-header">
            <button class="dpc-close-drawer" onclick="dpcToggleFilterDrawer()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- Sort By Section -->
        <div class="dpc-sort-section">
            <h4>Sort By</h4>
            <select id="dpc-sort-select-mobile" onchange="dpcSort(this.value)">
                <option value="default">Best Match</option>
                <option value="price_asc">Price: Low to High</option>
                <option value="price_desc">Price: High to Low</option>
                <option value="title_asc">Name: A-Z</option>
            </select>
        </div>

        <!-- Filters Section (Mobile - Accordion Style) -->
        <div class="dpc-filters-section">
            <div class="dpc-filter-header-inner">
                <h4>Filter</h4>
                <button type="button" class="dpc-clear-filters" onclick="dpcClearFilters()">Clear All</button>
            </div>

            <?php if (empty($attr_data)): ?>
                <p style="font-size:12px;color:#888;">No filters available.</p>
            <?php else: foreach ($attr_data as $label => $values):
                // Skip Bundle Quantity, Pallet Size, Brand, and Post Size from filters
                $label_lower = strtolower($label);
                if ($label_lower === 'bundle quantity' || $label_lower === 'bundle-quantity' ||
                    $label_lower === 'pallet size' || $label_lower === 'pallet-size' ||
                    $label_lower === 'brand' || $label_lower === 'post size' || $label_lower === 'post-size') continue;

                // Hide filter groups with no values
                if (empty($values)) continue;

                // Custom sorting for Length, Height, Width, Compatible Frame Size attributes (numeric ascending)
                if ($label_lower === 'length' || $label_lower === 'height' || $label_lower === 'width' || $label_lower === 'compatible frame size') {
                    uksort($values, function($a, $b) {
                        $num_a = floatval(preg_replace('/[^0-9.]/', '', $a));
                        $num_b = floatval(preg_replace('/[^0-9.]/', '', $b));
                        return $num_a - $num_b;
                    });
                }

                // Custom sorting for Product Size (WIDTHxLENGTH format) - sort by width first, then length
                if ($label_lower === 'product size') {
                    uksort($values, function($a, $b) {
                        // Extract all numbers from the value
                        preg_match_all('/[\d.]+/', $a, $matches_a);
                        preg_match_all('/[\d.]+/', $b, $matches_b);

                        $w_a = isset($matches_a[0][0]) ? floatval($matches_a[0][0]) : 0;
                        $l_a = isset($matches_a[0][1]) ? floatval($matches_a[0][1]) : 0;
                        $w_b = isset($matches_b[0][0]) ? floatval($matches_b[0][0]) : 0;
                        $l_b = isset($matches_b[0][1]) ? floatval($matches_b[0][1]) : 0;

                        // Sort by width first, then by length
                        if ($w_a !== $w_b) return $w_a - $w_b;
                        return $l_a - $l_b;
                    });
                }

                $safe = esc_attr(strtolower(str_replace(' ', '_', $label)));
            ?>
                <div class="dpc-filter-group-accordion">
                    <button class="dpc-filter-group-title" onclick="dpcToggleAccordion(this)">
                        <span><?php echo esc_html($label); ?></span>
                        <svg class="dpc-accordion-icon" viewBox="0 0 12 12" fill="currentColor">
                            <path d="M6 9L1 4h10z"/>
                        </svg>
                    </button>
                    <div class="dpc-filter-group-content">
                        <ul>
                            <?php foreach ($values as $val => $cnt):
                                $cb_id = 'dpc_mobile_' . $safe . '_' . md5($val);
                            ?>
                            <li data-original-count="<?php echo intval($cnt); ?>">
                                <label for="<?php echo $cb_id; ?>">
                                    <input type="checkbox" id="<?php echo $cb_id; ?>" class="dpc-filter-cb"
                                           data-attr="<?php echo esc_attr($label); ?>" data-val="<?php echo esc_attr($val); ?>">
                                    <?php echo esc_html($val); ?>
                                    <span class="dpc-fc">(<?php echo intval($cnt); ?>)</span>
                                </label>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </aside>

    <!-- Filter Drawer Overlay -->
    <div class="dpc-filter-overlay" id="dpc-filter-overlay" onclick="dpcToggleFilterDrawer()"></div>

    <div class="dpc-products-layout">
        <!-- Desktop Left Sidebar -->
        <aside class="dpc-filters-sidebar" id="dpc-filters-sidebar">
            <div class="dpc-filter-header-desktop">
                <h3>Filters</h3>
                <button type="button" id="dpc-clear-btn" class="dpc-clear-all" onclick="dpcClearFilters()">Clear All</button>
            </div>
            <?php if (empty($attr_data)): ?>
                <p style="font-size:12px;color:#888;">No filters available.</p>
            <?php else:
                // Reset the array pointer for second iteration
                reset($attr_data);
                foreach ($attr_data as $label => $values):
                    // Skip Bundle Quantity, Pallet Size, Brand, and Post Size from filters
                    $label_lower = strtolower($label);
                    if ($label_lower === 'bundle quantity' || $label_lower === 'bundle-quantity' ||
                        $label_lower === 'pallet size' || $label_lower === 'pallet-size' ||
                        $label_lower === 'brand' || $label_lower === 'post size' || $label_lower === 'post-size') continue;

                    // Hide filter groups with no values
                    if (empty($values)) continue;

                    $safe = esc_attr(strtolower(str_replace(' ', '_', $label)));
            ?>
                <div class="dpc-filter-group">
                    <h4><?php echo esc_html($label); ?></h4>
                    <ul>
                        <?php foreach ($values as $val => $cnt):
                            $cb_id = 'dpc_desktop_' . $safe . '_' . md5($val);
                        ?>
                        <li data-original-count="<?php echo intval($cnt); ?>">
                            <label for="<?php echo $cb_id; ?>">
                                <input type="checkbox" id="<?php echo $cb_id; ?>" class="dpc-filter-cb"
                                       data-attr="<?php echo esc_attr($label); ?>" data-val="<?php echo esc_attr($val); ?>">
                                <?php echo esc_html($val); ?>
                                <span class="dpc-fc">(<?php echo intval($cnt); ?>)</span>
                            </label>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; endif; ?>
        </aside>

        <div>
            <div class="dpc-sort-bar">
                Sort By | <select onchange="dpcSort(this.value)">
                    <option value="default">Best Match</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                    <option value="title_asc">Name: A-Z</option>
                </select>
            </div>
            <div class="dpc-products-grid" id="dpc-grid">
                <?php if (empty($all_products)): ?>
                    <p class="dpc-no-results">No products found.</p>
                <?php else: foreach ($all_products as $p):
                    $attrs_json = esc_attr(json_encode($p['attrs']));
                ?>
                    <div class="dpc-product-card"
                         data-attrs="<?php echo $attrs_json; ?>"
                         data-title="<?php echo esc_attr($p['title']); ?>"
                         data-price="<?php echo esc_attr(strip_tags($p['price_html'])); ?>">
                        <a href="<?php echo esc_url($p['permalink']); ?>" class="dpc-img-wrap">
                            <?php echo $p['thumbnail']; ?>
                        </a>
                        <div class="dpc-card-body">
                            <h4><a href="<?php echo esc_url($p['permalink']); ?>"><?php echo esc_html($p['title']); ?></a></h4>
                            <div class="dpc-price"><?php echo wp_kses_post($p['price_html']); ?></div>
                            <a href="<?php echo $p['add_to_cart']; ?>" class="dpc-add-to-cart-btn">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                                </svg>
                                Add to Cart
                            </a>
                        </div>
                    </div>
                <?php endforeach;
                echo '<p class="dpc-no-results" style="display:none;">No products match your filters.</p>';
                endif; ?>
            </div>
        </div>
    </div>
    <?php
}
?>
