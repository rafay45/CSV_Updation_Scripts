<?php
// Custom shortcode for dynamic category display with card UI
add_shortcode('dynamic_product_categories', 'display_dynamic_product_categories');
function display_dynamic_product_categories() {
    ob_start();

    $main_category_slugs = array('vinyl', 'ornamental', 'chain-link', 'simtek', 'modern', 'trex', 'wood', 'agriculture', 'metal-horse-fence');

    $parent_cats = get_terms(array(
        'taxonomy'   => 'product_cat',
        'slug'       => $main_category_slugs,
        'hide_empty' => true,
    ));

    ?>
    <style>
    .main-categories-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }
    .category-card {
        background: #fff;
        border: 2px solid #e5e5e5;
        border-radius: 16px;
        padding: 20px 20px 0;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: visible;
        position: relative;
        margin-bottom: 30px;
    }
    .category-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        border-color: #32703B;
    }
    .category-image {
        width: 100%;
        height: 180px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        border-radius: 8px;
        overflow: hidden;
    }
    .category-image img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
    }
    .category-button {
        background: #32703B;
        color: white;
        padding: 12px 20px;
        text-align: center;
        font-weight: 600;
        font-size: 16px;
        border: none;
        width: calc(100% - 40px);
        margin: 0 20px;
        border-radius: 25px;
        position: relative;
        display: block;
        transform: translateY(50%);
    }
    .category-button span {
        margin-left: 8px;
        font-size: 18px;
    }
    #sub-categories-display {
        margin-top: 60px;
        padding-top: 40px;
        border-top: 3px solid #32703B;
    }
    .sub-categories-header {
        text-align: left;
        margin-bottom: 30px;
    }
    .sub-categories-header h2 {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .sub-category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
    }
    .sub-category-card {
        background: white;
        border: 2px solid #e5e5e5;
        border-radius: 16px;
        padding: 40px 20px 25px;
        text-align: center;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        position: relative;
        margin-bottom: 30px;
        overflow: visible;
        min-height: 180px;
    }
    .sub-category-card:hover {
        border-color: #32703B;
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }
    .sub-category-icon {
        width: 100px;
        height: 100px;
        margin: 0 auto;
        background: #fff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .sub-category-icon img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
    }
    .sub-category-name {
        font-size: 15px;
        font-weight: 600;
        color: #333;
        background: #fff;
        padding: 10px 20px;
        border-radius: 20px;
        border: 1px solid #e5e5e5;
        display: inline-block;
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translate(-50%, 50%);
        white-space: nowrap;
        min-width: 120px;
        text-align: center;
    }
    .sub-category-count {
        font-size: 12px;
        color: #999;
        margin-top: 5px;
    }
    .breadcrumb-nav {
        margin-bottom: 20px;
    }
    .breadcrumb-nav button {
        background: #32703B;
        color: white;
        padding: 8px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
    }
    .breadcrumb-nav button:hover {
        background: #255a2e;
    }
    /* ── Products layout ── */
    .dpc-products-layout {
        display: grid;
        grid-template-columns: 240px 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }
    @media (max-width: 768px) {
        .dpc-products-layout { grid-template-columns: 1fr; }
        .dpc-filters-sidebar { display: none; }
    }
    /* ── Filters sidebar ── */
    .dpc-filters-sidebar {
        background: #f9f9f9;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        padding: 20px;
        align-self: start;
        position: sticky;
        top: 20px;
    }
    .dpc-filters-sidebar h3 {
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        color: #333;
        margin: 0 0 16px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid #32703B;
    }
    .dpc-filter-group {
        margin-bottom: 18px;
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 14px;
    }
    .dpc-filter-group:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    .dpc-filter-group h4 {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: #444;
        margin: 0 0 10px 0;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .dpc-filter-group h4::after {
        content: '▾';
        font-size: 14px;
        color: #888;
    }
    .dpc-filter-group ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .dpc-filter-group ul li {
        margin-bottom: 7px;
    }
    .dpc-filter-group ul li label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #333;
        cursor: pointer;
        line-height: 1.4;
    }
    .dpc-filter-group ul li label input[type="checkbox"] {
        cursor: pointer;
        accent-color: #32703B;
        width: 14px;
        height: 14px;
        flex-shrink: 0;
    }
    .dpc-filter-group ul li label .dpc-count {
        margin-left: auto;
        color: #999;
        font-size: 11px;
    }
    /* ── Products grid ── */
    .dpc-products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: 16px;
    }
    /* ── Product card (screenshot style) ── */
    .dpc-product-card {
        border: 1px solid #dde3ec;
        border-radius: 6px;
        background: #fff;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: box-shadow 0.2s ease;
    }
    .dpc-product-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }
    .dpc-product-card .dpc-img-wrap {
        display: block;
        background: #f5f7fa;
        overflow: hidden;
        aspect-ratio: 1 / 1;
        text-decoration: none;
    }
    .dpc-product-card .dpc-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.3s ease;
    }
    .dpc-product-card:hover .dpc-img-wrap img {
        transform: scale(1.04);
    }
    .dpc-product-card .dpc-card-body {
        padding: 10px 12px 12px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .dpc-product-card h4 {
        font-size: 12px;
        line-height: 1.45;
        color: #222;
        margin: 0 0 8px 0;
        flex-grow: 1;
    }
    .dpc-product-card h4 a {
        color: #222;
        text-decoration: none;
    }
    .dpc-product-card h4 a:hover {
        color: #32703B;
    }
    .dpc-product-card .dpc-price {
        font-size: 15px;
        font-weight: 700;
        color: #32703B;
        margin-bottom: 10px;
    }
    .dpc-product-card .dpc-img-wrap {
        position: relative;
    }
    .dpc-add-to-cart-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        background: #32703B;
        color: white;
        border: none;
        padding: 9px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.2s ease;
        margin-top: auto;
        text-align: center;
    }
    .dpc-add-to-cart-btn:hover {
        background: #2a5330;
        color: white;
    }
    .dpc-add-to-cart-btn svg {
        flex-shrink: 0;
    }
    .dpc-no-results {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        color: #888;
        font-size: 15px;
    }
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
    </style>

    <div class="main-categories-wrapper">
        <div class="category-grid">
            <?php
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

            foreach ($parent_cats as $cat):
                $thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
                $image_url    = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
                if (!$image_url && isset($category_images[$cat->slug])) {
                    $image_url = $category_images[$cat->slug];
                }
            ?>
            <div class="category-card" onclick="dpcLoadSubCategories('<?php echo esc_js($cat->slug); ?>', <?php echo $cat->term_id; ?>, '<?php echo esc_js($cat->name); ?>')">
                <div class="category-image">
                    <?php if ($image_url): ?>
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($cat->name); ?>">
                    <?php else: ?>
                        <div style="font-size:80px;">🏗️</div>
                    <?php endif; ?>
                </div>
                <div class="category-button">
                    <?php echo esc_html($cat->name); ?> <span>›</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div id="sub-categories-display"></div>
    </div>

    <script>
    var dpcAjax = '<?php echo admin_url('admin-ajax.php'); ?>';

    function dpcLoadSubCategories(slug, parentId, parentName) {
        var d = document.getElementById('sub-categories-display');
        d.innerHTML = '<div style="text-align:center;padding:40px;">Loading...</div>';
        var xhr = new XMLHttpRequest();
        xhr.open('GET', dpcAjax + '?action=dpc_get_sub_categories&parent_id=' + parentId + '&parent_slug=' + encodeURIComponent(slug) + '&parent_name=' + encodeURIComponent(parentName), true);
        xhr.onload = function() { d.innerHTML = xhr.responseText; dpcScroll(); };
        xhr.onerror = function() { d.innerHTML = '<p style="color:red;text-align:center;">Error loading. Please try again.</p>'; };
        xhr.send();
    }

    function dpcLoadTags(subSlug, subName, parentSlug, parentName) {
        var d = document.getElementById('sub-categories-display');
        d.innerHTML = '<div style="text-align:center;padding:40px;">Loading tags...</div>';
        var xhr = new XMLHttpRequest();
        xhr.open('GET', dpcAjax + '?action=dpc_get_tags&sub_slug=' + encodeURIComponent(subSlug) + '&sub_name=' + encodeURIComponent(subName) + '&parent_slug=' + encodeURIComponent(parentSlug) + '&parent_name=' + encodeURIComponent(parentName), true);
        xhr.onload = function() { d.innerHTML = xhr.responseText; dpcScroll(); };
        xhr.onerror = function() { d.innerHTML = '<p style="color:red;text-align:center;">Error loading tags.</p>'; };
        xhr.send();
    }

    function dpcLoadProducts(subSlug, subName, parentSlug, parentName, tagSlug, tagName) {
        var d = document.getElementById('sub-categories-display');
        d.innerHTML = '<div style="text-align:center;padding:40px;">Loading products...</div>';
        var url = dpcAjax + '?action=dpc_get_products&sub_slug=' + encodeURIComponent(subSlug) + '&sub_name=' + encodeURIComponent(subName) + '&parent_slug=' + encodeURIComponent(parentSlug) + '&parent_name=' + encodeURIComponent(parentName);
        if (tagSlug) {
            url += '&tag_slug=' + encodeURIComponent(tagSlug) + '&tag_name=' + encodeURIComponent(tagName);
        }
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.onload = function() { d.innerHTML = xhr.responseText; dpcScroll(); dpcInitFilters(); };
        xhr.onerror = function() { d.innerHTML = '<p style="color:red;text-align:center;">Error loading products.</p>'; };
        xhr.send();
    }

    function dpcBackToCategories() {
        document.getElementById('sub-categories-display').innerHTML = '';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function dpcScroll() {
        setTimeout(function() {
            document.getElementById('sub-categories-display').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    }

    // ── Client-side attribute filtering ──────────────────────────────────────
    function dpcInitFilters() {
        var checkboxes = document.querySelectorAll('.dpc-filter-cb');
        checkboxes.forEach(function(cb) {
            cb.addEventListener('change', dpcApplyFilters);
        });
    }

    function dpcApplyFilters() {
        var active = {};
        document.querySelectorAll('.dpc-filter-cb:checked').forEach(function(cb) {
            var attr = cb.dataset.attr;
            var val  = cb.dataset.val;
            if (!active[attr]) active[attr] = [];
            active[attr].push(val.toLowerCase());
        });

        var cards = document.querySelectorAll('.dpc-product-card');
        cards.forEach(function(card) {
            if (Object.keys(active).length === 0) {
                card.style.display = '';
                return;
            }
            var attrs = JSON.parse(card.dataset.attrs || '{}');
            var show = true;
            for (var attr in active) {
                var cardVal = (attrs[attr] || '').toLowerCase();
                var matched = active[attr].some(function(v) { return cardVal.indexOf(v) !== -1; });
                if (!matched) { show = false; break; }
            }
            card.style.display = show ? '' : 'none';
        });

        var grid = document.querySelector('.dpc-products-grid');
        if (grid) {
            var visible = grid.querySelectorAll('.dpc-product-card:not([style*="display: none"])').length;
            var noRes = grid.querySelector('.dpc-no-results');
            if (noRes) noRes.style.display = visible === 0 ? '' : 'none';
        }
    }
    </script>
    <?php
    return ob_get_clean();
}

// ─── AJAX: Get Sub-Categories ────────────────────────────────────────────────
add_action('wp_ajax_dpc_get_sub_categories',        'dpc_ajax_get_sub_categories');
add_action('wp_ajax_nopriv_dpc_get_sub_categories', 'dpc_ajax_get_sub_categories');
function dpc_ajax_get_sub_categories() {
    $parent_id   = isset($_GET['parent_id'])   ? intval($_GET['parent_id'])               : 0;
    $parent_slug = isset($_GET['parent_slug']) ? sanitize_text_field($_GET['parent_slug']) : '';
    $parent_name = isset($_GET['parent_name']) ? sanitize_text_field($_GET['parent_name']) : '';

    // ✅ FIX 1: Get ALL subcategories without hide_empty restriction
    // hide_empty => false because products might be in nested child categories
    $sub_cats = get_terms(array(
        'taxonomy'   => 'product_cat',
        'parent'     => $parent_id,
        'hide_empty' => false,  // Changed to false to show all subcategories
        // 'number' removed - ab saari subcategories aayengi
    ));

    echo '<div class="breadcrumb-nav"><button onclick="dpcBackToCategories()">← Back to Categories</button></div>';

    if (!empty($sub_cats) && !is_wp_error($sub_cats)) {
        echo '<div class="sub-categories-header"><h2>' . esc_html($parent_name) . ' - SUB-CATEGORIES</h2></div>';
        echo '<div class="sub-category-grid">';
        foreach ($sub_cats as $sub) {
            $thumbnail_id = get_term_meta($sub->term_id, 'thumbnail_id', true);
            $icon_url     = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
            
            // ✅ FIX: Get accurate product count including child categories
            $product_count = dpc_get_category_product_count($sub->term_id);
            ?>
            <div onclick="dpcLoadTags('<?php echo esc_js($sub->slug); ?>','<?php echo esc_js($sub->name); ?>','<?php echo esc_js($parent_slug); ?>','<?php echo esc_js($parent_name); ?>')" style="cursor:pointer;">
                <a href="javascript:void(0);" class="sub-category-card">
                    <div class="sub-category-icon">
                        <?php if ($icon_url): ?>
                            <img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($sub->name); ?>">
                        <?php else: ?>
                            <span style="font-size:40px;"></span>
                        <?php endif; ?>
                    </div>
                    <div class="sub-category-name"><?php echo esc_html($sub->name); ?></div>
                    <div class="sub-category-count"><?php echo intval($product_count); ?> products</div>
                </a>
            </div>
            <?php
        }
        echo '</div>';
    } else {
        echo '<div class="sub-categories-header"><h2>' . esc_html($parent_name) . ' - PRODUCTS</h2></div>';
        dpc_render_products_with_filters(array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $parent_id,
        ), $parent_slug, $parent_name, '', '', null);
    }
    wp_die();
}

// ─── AJAX: Get Tags for Sub-Category ────────────────────────────────────────
add_action('wp_ajax_dpc_get_tags',        'dpc_ajax_get_tags');
add_action('wp_ajax_nopriv_dpc_get_tags', 'dpc_ajax_get_tags');
function dpc_ajax_get_tags() {
    $sub_slug    = isset($_GET['sub_slug'])    ? sanitize_text_field($_GET['sub_slug'])    : '';
    $sub_name    = isset($_GET['sub_name'])    ? sanitize_text_field($_GET['sub_name'])    : '';
    $parent_slug = isset($_GET['parent_slug']) ? sanitize_text_field($_GET['parent_slug']) : '';
    $parent_name = isset($_GET['parent_name']) ? sanitize_text_field($_GET['parent_name']) : '';

    $parent_cat = get_term_by('slug', $parent_slug, 'product_cat');
    $parent_id  = $parent_cat ? $parent_cat->term_id : 0;

    $sub_term = get_term_by('slug', $sub_slug, 'product_cat');
    $sub_id   = $sub_term ? $sub_term->term_id : 0;

    if (!$sub_id) {
        echo '<p style="text-align:center;padding:40px;color:#888;">Sub-category not found.</p>';
        wp_die();
    }

    // ✅ FIX: Get all child categories to include their products too
    $category_ids = array($sub_id);
    $child_terms = get_term_children($sub_id, 'product_cat');
    if (!is_wp_error($child_terms) && !empty($child_terms)) {
        $category_ids = array_merge($category_ids, $child_terms);
    }

    // Collect tags from products in this sub-category AND child categories
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category_ids,  // Include child categories
            )
        )
    );
    $query = new WP_Query($args);
    $all_tags = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $tags = get_the_terms(get_the_ID(), 'product_tag');
            if ($tags && !is_wp_error($tags)) {
                foreach ($tags as $tag) {
                    if (!isset($all_tags[$tag->term_id])) {
                        $all_tags[$tag->term_id] = array(
                            'name' => $tag->name,
                            'slug' => $tag->slug,
                            'count' => 0
                        );
                    }
                    $all_tags[$tag->term_id]['count']++;
                }
            }
        }
        wp_reset_postdata();
    }

    // ✅ DEBUG: Log all tags found (remove this after debugging)
    error_log('DPC Tags Debug for ' . $sub_name . ':');
    error_log('Total products found: ' . $query->found_posts);
    error_log('Total tags collected: ' . count($all_tags));
    foreach ($all_tags as $t) {
        error_log('  - ' . $t['name'] . ' (' . $t['slug'] . '): ' . $t['count'] . ' products');
    }

    // ✅ FIX 2: Show ALL tags instead of filtering by fence-style keywords
    // Purana code sirf specific fence-style tags dikha raha tha
    // Ab saare tags dikhengy
    
    ?>
    <div class="breadcrumb-nav">
        <button onclick="dpcLoadSubCategories('<?php echo esc_js($parent_slug); ?>', <?php echo intval($parent_id); ?>, '<?php echo esc_js($parent_name); ?>')">← Back to <?php echo esc_html($parent_name); ?></button>
    </div>
    <?php

    if (empty($all_tags)) {
        // No tags found - show products directly
        echo '<div class="sub-categories-header"><h2>' . esc_html($parent_name) . ' / ' . esc_html($sub_name) . '</h2></div>';
        dpc_render_products_with_filters(array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $sub_id,
            'include_children' => true,
        ), $parent_slug, $parent_name, $sub_slug, $sub_name, null);
    } else {
        // Show ALL tags
        echo '<div class="sub-categories-header">';
        echo '<h2>' . esc_html($parent_name) . ' / ' . esc_html($sub_name) . ' - TAGS</h2>';
        
        // ✅ DEBUG: Show all tag names found
        $tag_names = array_map(function($t) { return $t['name']; }, $all_tags);
        echo '<p>Select a tag to filter products</p>';
        echo '<p style="font-size:12px;color:#666;">Debug: Found ' . count($all_tags) . ' tags from ' . $query->found_posts . ' products<br>';
        echo 'Tags: ' . implode(', ', $tag_names) . '</p>';
        
        echo '</div>';
        echo '<div class="sub-category-grid">';
        
        // Sort tags alphabetically by name
        usort($all_tags, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        
        foreach ($all_tags as $tag) {
            ?>
            <div onclick="dpcLoadProducts('<?php echo esc_js($sub_slug); ?>','<?php echo esc_js($sub_name); ?>','<?php echo esc_js($parent_slug); ?>','<?php echo esc_js($parent_name); ?>','<?php echo esc_js($tag['slug']); ?>','<?php echo esc_js($tag['name']); ?>')" style="cursor:pointer;">
                <a href="javascript:void(0);" class="sub-category-card">
                    <div class="sub-category-icon">
                        <span style="font-size:40px;color:#32703B;"></span>
                    </div>
                    <div class="sub-category-name"><?php echo esc_html($tag['name']); ?></div>
                    <div class="sub-category-count"><?php echo intval($tag['count']); ?> products</div>
                </a>
            </div>
            <?php
        }
        echo '</div>';
    }

    wp_die();
}

// ─── AJAX: Get Products ──────────────────────────────────────────────────────
add_action('wp_ajax_dpc_get_products',        'dpc_ajax_get_products');
add_action('wp_ajax_nopriv_dpc_get_products', 'dpc_ajax_get_products');
function dpc_ajax_get_products() {
    $sub_slug    = isset($_GET['sub_slug'])    ? sanitize_text_field($_GET['sub_slug'])    : '';
    $sub_name    = isset($_GET['sub_name'])    ? sanitize_text_field($_GET['sub_name'])    : '';
    $parent_slug = isset($_GET['parent_slug']) ? sanitize_text_field($_GET['parent_slug']) : '';
    $parent_name = isset($_GET['parent_name']) ? sanitize_text_field($_GET['parent_name']) : '';
    $tag_slug    = isset($_GET['tag_slug'])    ? sanitize_text_field($_GET['tag_slug'])    : '';
    $tag_name    = isset($_GET['tag_name'])    ? sanitize_text_field($_GET['tag_name'])    : '';

    $parent_cat = get_term_by('slug', $parent_slug, 'product_cat');
    $parent_id  = $parent_cat ? $parent_cat->term_id : 0;

    $sub_term = get_term_by('slug', $sub_slug, 'product_cat');
    $sub_id   = $sub_term ? $sub_term->term_id : 0;

    ?>
    <div class="breadcrumb-nav">
        <?php if ($tag_slug): ?>
            <button onclick="dpcLoadTags('<?php echo esc_js($sub_slug); ?>','<?php echo esc_js($sub_name); ?>','<?php echo esc_js($parent_slug); ?>','<?php echo esc_js($parent_name); ?>')">← Back to Tags</button>
        <?php else: ?>
            <button onclick="dpcLoadSubCategories('<?php echo esc_js($parent_slug); ?>', <?php echo intval($parent_id); ?>, '<?php echo esc_js($parent_name); ?>')">← Back to <?php echo esc_html($parent_name); ?></button>
        <?php endif; ?>
    </div>
    <div class="sub-categories-header">
        <h2>
            <?php echo esc_html($parent_name); ?> / <?php echo esc_html($sub_name); ?>
            <?php if ($tag_name): ?>
                / <?php echo esc_html($tag_name); ?>
            <?php endif; ?>
        </h2>
    </div>
    <?php

    if (!$sub_id) {
        echo '<p style="text-align:center;padding:40px;color:#888;">Category not found.</p>';
        wp_die();
    }

    // Build tax query
    $tax_query = array(
        'taxonomy' => 'product_cat',
        'field'    => 'term_id',
        'terms'    => $sub_id,
        'include_children' => true,
    );

    dpc_render_products_with_filters($tax_query, $parent_slug, $parent_name, $sub_slug, $sub_name, $tag_slug);

    wp_die();
}

// ─── Helper: Render products with filters ────────────────────────────────────
function dpc_render_products_with_filters($tax_query_item, $parent_slug, $parent_name, $sub_slug, $sub_name, $tag_slug = null, $posts_per_page = 500) {
    
    $tax_query = array($tax_query_item);
    
    // Add tag filter if provided
    if ($tag_slug) {
        $tax_query['relation'] = 'AND';
        $tax_query[] = array(
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => $tag_slug,
        );
    }

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => $posts_per_page,
        'tax_query'      => $tax_query,
        'orderby'        => 'title',
        'order'          => 'ASC',
    );
    $query = new WP_Query($args);

    $attr_data    = array();
    $all_products = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $pid     = get_the_ID();
            $product = wc_get_product($pid);
            if (!$product) continue;

            $attrs_for_card = array();

            foreach ($product->get_attributes() as $attr_slug => $attr_obj) {
                $label = wc_attribute_label($attr_slug);
                $value = '';

                if (is_object($attr_obj) && method_exists($attr_obj, 'is_taxonomy')) {
                    if ($attr_obj->is_taxonomy()) {
                        $terms = wp_get_post_terms($pid, $attr_slug, array('fields' => 'names'));
                        $value = (!is_wp_error($terms) && is_array($terms)) ? implode(', ', $terms) : '';
                    } else {
                        $opts  = $attr_obj->get_options();
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
                'id'         => $pid,
                'title'      => get_the_title(),
                'permalink'  => get_permalink(),
                'add_to_cart'=> esc_url(wc_get_cart_url() . '?add-to-cart=' . $pid),
                'price_html' => $product->get_price_html(),
                'thumbnail'  => get_the_post_thumbnail($pid, array(300, 300), array('style' => 'width:100%;height:100%;object-fit:cover;display:block;')),
                'attrs'      => $attrs_for_card,
            );
        }
        wp_reset_postdata();
    }
    ksort($attr_data);
    foreach ($attr_data as $label => &$vals) {
        ksort($vals);
    }
    unset($vals);

    echo '<div class="dpc-products-layout">';

    // LEFT: Filters
    echo '<aside class="dpc-filters-sidebar">';
    echo '<h3>Filters</h3>';

    if (empty($attr_data)) {
        echo '<p style="font-size:13px;color:#888;">No filters available.</p>';
    } else {
        foreach ($attr_data as $label => $values) {
            $safe_label = esc_attr(strtolower(str_replace(' ', '_', $label)));
            echo '<div class="dpc-filter-group">';
            echo '<h4>' . esc_html($label) . '</h4>';
            echo '<ul>';
            foreach ($values as $val => $count) {
                $cb_id = 'dpc_' . $safe_label . '_' . esc_attr(md5($val));
                echo '<li><label for="' . $cb_id . '">';
                echo '<input type="checkbox" id="' . $cb_id . '" class="dpc-filter-cb" data-attr="' . esc_attr($label) . '" data-val="' . esc_attr($val) . '">';
                echo esc_html($val);
                echo '<span class="dpc-count">(' . intval($count) . ')</span>';
                echo '</label></li>';
            }
            echo '</ul>';
            echo '</div>';
        }
    }
    echo '</aside>';

    // RIGHT: Products
    echo '<div>';
    echo '<div class="dpc-sort-bar">Sort By | <select onchange="dpcSort(this.value)"><option value="default">Best Match</option><option value="price_asc">Price: Low to High</option><option value="price_desc">Price: High to Low</option><option value="title_asc">Name: A-Z</option></select></div>';
    echo '<div class="dpc-products-grid" id="dpc-grid">';

    if (empty($all_products)) {
        echo '<p class="dpc-no-results">No products found.</p>';
    } else {
        foreach ($all_products as $p) {
            $attrs_json = esc_attr(json_encode($p['attrs']));
            echo '<div class="dpc-product-card" data-attrs="' . $attrs_json . '" data-title="' . esc_attr($p['title']) . '" data-price="' . esc_attr(strip_tags($p['price_html'])) . '">';
            echo '<a href="' . esc_url($p['permalink']) . '" class="dpc-img-wrap">';
            echo $p['thumbnail'];
            echo '</a>';
            echo '<div class="dpc-card-body">';
            echo '<h4><a href="' . esc_url($p['permalink']) . '">' . esc_html($p['title']) . '</a></h4>';
            echo '<div class="dpc-price">' . wp_kses_post($p['price_html']) . '</div>';
            echo '<a href="' . $p['add_to_cart'] . '" class="dpc-add-to-cart-btn">';
            echo '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>';
            echo 'Add to Cart';
            echo '</a>';
            echo '</div>';
            echo '</div>';
        }
        echo '<p class="dpc-no-results" style="display:none;">No products match your filters.</p>';
    }

    echo '</div>';
    echo '</div>';
    echo '</div>';

    ?>
    <script>
    function dpcSort(val) {
        var grid = document.getElementById('dpc-grid');
        if (!grid) return;
        var cards = Array.from(grid.querySelectorAll('.dpc-product-card'));
        cards.sort(function(a, b) {
            if (val === 'title_asc') {
                return a.dataset.title.localeCompare(b.dataset.title);
            }
            if (val === 'price_asc' || val === 'price_desc') {
                var pa = parseFloat(a.dataset.price.replace(/[^0-9.]/g, '')) || 0;
                var pb = parseFloat(b.dataset.price.replace(/[^0-9.]/g, '')) || 0;
                return val === 'price_asc' ? pa - pb : pb - pa;
            }
            return 0;
        });
        cards.forEach(function(c) { grid.appendChild(c); });
    }
    </script>
    <?php
}

// ─── Helper: Get accurate product count including child categories ──────────
function dpc_get_category_product_count($term_id) {
    // Get the category and all its child categories
    $term_ids = array($term_id);
    
    // Get all child categories recursively
    $child_terms = get_term_children($term_id, 'product_cat');
    if (!is_wp_error($child_terms) && !empty($child_terms)) {
        $term_ids = array_merge($term_ids, $child_terms);
    }
    
    // Count products in this category and all children
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $term_ids,
            )
        )
    );
    
    $products = new WP_Query($args);
    $count = $products->found_posts;
    wp_reset_postdata();
    
    return $count;
}
?>
