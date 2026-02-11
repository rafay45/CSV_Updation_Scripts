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
    .product-card {
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        background: white;
    }
    .product-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-3px);
    }
    .product-card a.product-img {
        display: block;
        overflow: hidden;
        background: #f5f5f5;
        text-decoration: none;
        aspect-ratio: 1 / 1;
    }
    .product-card a.product-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .product-card .product-info {
        padding: 12px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .product-card h4 {
        font-size: 12px;
        margin: 0 0 8px 0;
        line-height: 1.4;
        color: #333;
        flex-grow: 1;
    }
    .product-card h4 a {
        color: #333;
        text-decoration: none;
    }
    .product-card .product-price {
        font-size: 13px;
        font-weight: 700;
        color: #0066cc;
        margin-bottom: 10px;
    }
    .product-card .btn-view {
        display: block;
        width: 100%;
        background: #32703B;
        color: white;
        border: none;
        padding: 8px 5px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        margin-top: auto;
    }
    .product-card .btn-view:hover {
        background: #255a2e;
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
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    </style>

    <div class="main-categories-wrapper">
        <div class="category-grid">
            <?php
            $category_images = array(
                'chain-link'       => 'https://staging2.wholesalefencing.com/wp-content/uploads/2023/05/chainlink-fence-with-vinyl-slats-150x150.png',
                'modern'           => 'https://wholesalefencing.com/wp-content/uploads/2025/01/Privacy.webp',
                'vinyl'            => 'https://wholesalefencing.com/wp-content/uploads/2022/10/white-2.png',
                'simtek'           => 'https://wholesalefencing.com/wp-content/uploads/2022/10/EcoStone-6x6Panel-beige-wholesale-viinyl-fencing-1.jpg',
                'wood'             => 'https://staging2.wholesalefencing.com/wp-content/uploads/2023/05/ashland-simulated-wood-grain-150x150.jpeg',
                'agriculture'      => 'https://wholesalefencing.com/wp-content/uploads/2023/03/H2bb27b9f5b1940eea84dadc664bdd640u.png',
                'trex'             => 'https://wholesalefencing.com/wp-content/uploads/2022/10/IMG_1911-scaled.jpg',
                'ornamental'       => 'https://wholesalefencing.com/wp-content/uploads/2023/03/H2bb27b9f5b1940eea84dadc664bdd640u.png',
                'metal-horse-fence'=> 'https://wholesalefencing.com/wp-content/uploads/2023/03/H2bb27b9f5b1940eea84dadc664bdd640u.png',
            );

            foreach ($parent_cats as $cat):
                $thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
                $image_url    = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
                if (!$image_url && isset($category_images[$cat->slug])) {
                    $image_url = $category_images[$cat->slug];
                }
            ?>
            <div class="category-card" onclick="loadSubCategories('<?php echo esc_js($cat->slug); ?>', <?php echo $cat->term_id; ?>, '<?php echo esc_js($cat->name); ?>')">
                <div class="category-image">
                    <?php if ($image_url): ?>
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($cat->name); ?>">
                    <?php else: ?>
                        <div style="font-size: 80px;">🏗️</div>
                    <?php endif; ?>
                </div>
                <div class="category-button">
                    <?php echo esc_html($cat->name); ?>
                    <span>›</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div id="sub-categories-display"></div>
    </div>

    <script>
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

    function loadSubCategories(slug, parentId, parentName) {
        var display = document.getElementById('sub-categories-display');
        display.innerHTML = '<div style="text-align:center;padding:40px;"><p>Loading...</p></div>';
        var xhr = new XMLHttpRequest();
        xhr.open('GET', ajaxurl + '?action=get_sub_categories&parent_id=' + parentId + '&parent_slug=' + encodeURIComponent(slug) + '&parent_name=' + encodeURIComponent(parentName), true);
        xhr.onload = function() {
            display.innerHTML = xhr.responseText;
            setTimeout(function() { display.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 100);
        };
        xhr.onerror = function() { display.innerHTML = '<p style="text-align:center;color:red;">Error loading. Please try again.</p>'; };
        xhr.send();
    }

    function loadProductsBySubcategory(subSlug, subName, parentSlug, parentName) {
        var display = document.getElementById('sub-categories-display');
        display.innerHTML = '<div style="text-align:center;padding:40px;"><p>Loading products...</p></div>';
        var xhr = new XMLHttpRequest();
        xhr.open('GET', ajaxurl + '?action=get_products_by_subcategory&sub_slug=' + encodeURIComponent(subSlug) + '&sub_name=' + encodeURIComponent(subName) + '&parent_slug=' + encodeURIComponent(parentSlug) + '&parent_name=' + encodeURIComponent(parentName), true);
        xhr.onload = function() {
            display.innerHTML = xhr.responseText;
            setTimeout(function() { display.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 100);
        };
        xhr.onerror = function() { display.innerHTML = '<p style="text-align:center;color:red;">Error loading products. Please try again.</p>'; };
        xhr.send();
    }

    function goBackToCategories() {
        document.getElementById('sub-categories-display').innerHTML = '';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    </script>
    <?php
    return ob_get_clean();
}

// ─── AJAX: Get Sub-Categories ───────────────────────────────────────────────
add_action('wp_ajax_get_sub_categories', 'ajax_get_sub_categories');
add_action('wp_ajax_nopriv_get_sub_categories', 'ajax_get_sub_categories');
function ajax_get_sub_categories() {
    $parent_id   = isset($_GET['parent_id'])   ? intval($_GET['parent_id'])                     : 0;
    $parent_slug = isset($_GET['parent_slug']) ? sanitize_text_field($_GET['parent_slug'])       : '';
    $parent_name = isset($_GET['parent_name']) ? sanitize_text_field($_GET['parent_name'])       : '';

    $sub_cats = get_terms(array(
        'taxonomy'   => 'product_cat',
        'parent'     => $parent_id,
        'hide_empty' => true,
        'number'     => 100,
    ));

    if (!empty($sub_cats) && !is_wp_error($sub_cats)) {
        // Has sub-categories — show them
        ?>
        <div class="breadcrumb-nav">
            <button onclick="goBackToCategories()">← Back to Categories</button>
        </div>
        <div class="sub-categories-header">
            <h2><?php echo esc_html($parent_name); ?> - SUB-CATEGORIES</h2>
        </div>
        <div class="sub-category-grid">
            <?php foreach ($sub_cats as $sub):
                $thumbnail_id = get_term_meta($sub->term_id, 'thumbnail_id', true);
                $icon_url     = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
            ?>
            <div onclick="loadProductsBySubcategory('<?php echo esc_js($sub->slug); ?>', '<?php echo esc_js($sub->name); ?>', '<?php echo esc_js($parent_slug); ?>', '<?php echo esc_js($parent_name); ?>')" style="cursor:pointer;">
                <a href="javascript:void(0);" class="sub-category-card">
                    <div class="sub-category-icon">
                        <?php if ($icon_url): ?>
                            <img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($sub->name); ?>">
                        <?php else: ?>
                            <span style="font-size:40px;">📦</span>
                        <?php endif; ?>
                    </div>
                    <div class="sub-category-name"><?php echo esc_html($sub->name); ?></div>
                    <div class="sub-category-count"><?php echo intval($sub->count); ?> products</div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
    } else {
        // No sub-categories — show products directly
        ?>
        <div class="breadcrumb-nav">
            <button onclick="goBackToCategories()">← Back to Categories</button>
        </div>
        <div class="sub-categories-header">
            <h2><?php echo esc_html($parent_name); ?> - PRODUCTS</h2>
        </div>
        <?php
        dpc_render_products(array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $parent_id,
        ));
    }
    wp_die();
}

// ─── AJAX: Get Products by Sub-Category ─────────────────────────────────────
add_action('wp_ajax_get_products_by_subcategory', 'ajax_get_products_by_subcategory');
add_action('wp_ajax_nopriv_get_products_by_subcategory', 'ajax_get_products_by_subcategory');
function ajax_get_products_by_subcategory() {
    $sub_slug    = isset($_GET['sub_slug'])    ? sanitize_text_field($_GET['sub_slug'])    : '';
    $sub_name    = isset($_GET['sub_name'])    ? sanitize_text_field($_GET['sub_name'])    : '';
    $parent_slug = isset($_GET['parent_slug']) ? sanitize_text_field($_GET['parent_slug']) : '';
    $parent_name = isset($_GET['parent_name']) ? sanitize_text_field($_GET['parent_name']) : '';

    $parent_cat = get_term_by('slug', $parent_slug, 'product_cat');
    $parent_id  = $parent_cat ? $parent_cat->term_id : 0;

    $sub_term = get_term_by('slug', $sub_slug, 'product_cat');
    $sub_id   = $sub_term ? $sub_term->term_id : 0;

    if (!$sub_id) {
        echo '<p style="text-align:center;padding:40px;">Category not found.</p>';
        wp_die();
    }
    ?>
    <div class="breadcrumb-nav">
        <button onclick="loadSubCategories('<?php echo esc_js($parent_slug); ?>', <?php echo intval($parent_id); ?>, '<?php echo esc_js($parent_name); ?>')">← Back to <?php echo esc_html($parent_name); ?></button>
    </div>
    <div class="sub-categories-header">
        <h2><?php echo esc_html($parent_name); ?> / <?php echo esc_html($sub_name); ?></h2>
        <p>Showing all <?php echo esc_html($sub_name); ?> products</p>
    </div>
    <?php
    dpc_render_products(array(
        'taxonomy'         => 'product_cat',
        'field'            => 'term_id',
        'terms'            => $sub_id,
        'include_children' => true,
    ));
    wp_die();
}

// ─── Helper: Render Products Grid ───────────────────────────────────────────
function dpc_render_products($tax_query_item, $posts_per_page = 24) {
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => $posts_per_page,
        'paged'          => max(1, get_query_var('paged')),
        'tax_query'      => array($tax_query_item),
    );

    $products = new WP_Query($args);

    echo '<div class="products-grid">';
    if ($products->have_posts()) {
        while ($products->have_posts()) {
            $products->the_post();
            $product = wc_get_product(get_the_ID());
            $price   = $product ? $product->get_price_html() : '';
            $img     = get_the_post_thumbnail(get_the_ID(), array(300, 300), array('style' => 'width:100%;height:100%;object-fit:cover;display:block;'));
            ?>
            <div class="product-card">
                <a href="<?php echo esc_url(get_permalink()); ?>" class="product-img">
                    <?php echo $img; ?>
                </a>
                <div class="product-info">
                    <h4><a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_the_title()); ?></a></h4>
                    <div class="product-price"><?php echo wp_kses_post($price); ?></div>
                    <a href="<?php echo esc_url(get_permalink()); ?>" class="btn-view">View Product</a>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<p style="grid-column:1/-1;text-align:center;padding:40px;">No products found.</p>';
    }
    echo '</div>';

    if ($products->max_num_pages > 1) {
        echo '<div style="text-align:center;margin-top:30px;">';
        echo paginate_links(array(
            'total'   => $products->max_num_pages,
            'current' => max(1, get_query_var('paged')),
            'type'    => 'list',
        ));
        echo '</div>';
    }

    wp_reset_postdata();
}
