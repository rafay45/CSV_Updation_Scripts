<?php
// Custom shortcode for dynamic category display with card UI
add_shortcode('dynamic_product_categories', 'display_dynamic_product_categories');
function display_dynamic_product_categories() {
    ob_start();

    // Define main categories only
    $main_category_slugs = array('vinyl', 'ornamental', 'chain-link', 'simtek', 'modern', 'trex', 'wood', 'agriculture', 'metal-horse-fence');

    // Get only main fence type categories
    $parent_cats = get_terms(array(
        'taxonomy' => 'product_cat',
        'slug' => $main_category_slugs,
        'hide_empty' => true
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
        transition: transform 0.3s ease;
    }

    /* Sub-categories section */
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
    </style>

    <div class="main-categories-wrapper">
        <div class="category-grid">
            <?php
            // Define category images
            $category_images = array(
                'chain-link' => 'https://staging2.wholesalefencing.com/wp-content/uploads/2023/05/chainlink-fence-with-vinyl-slats-150x150.png',
                'modern' => 'https://wholesalefencing.com/wp-content/uploads/2025/01/Privacy.webp',
                'vinyl' => 'https://wholesalefencing.com/wp-content/uploads/2022/10/white-2.png',
                'simtek' => 'https://wholesalefencing.com/wp-content/uploads/2022/10/EcoStone-6x6Panel-beige-wholesale-viinyl-fencing-1.jpg',
                'wood' => 'https://staging2.wholesalefencing.com/wp-content/uploads/2023/05/ashland-simulated-wood-grain-150x150.jpeg',
                'agriculture' => 'https://wholesalefencing.com/wp-content/uploads/2023/03/H2bb27b9f5b1940eea84dadc664bdd640u.png',
                'trex' => 'https://wholesalefencing.com/wp-content/uploads/2022/10/IMG_1911-scaled.jpg',
                'ornamental' => 'https://wholesalefencing.com/wp-content/uploads/2023/03/H2bb27b9f5b1940eea84dadc664bdd640u.png',
                'metal-horse-fence' => 'https://wholesalefencing.com/wp-content/uploads/2023/03/H2bb27b9f5b1940eea84dadc664bdd640u.png'
            );

            foreach ($parent_cats as $cat):
                // Get image from hardcoded array or category meta
                $thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
                $image_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';

                // If no image in category meta, use hardcoded image
                if (!$image_url && isset($category_images[$cat->slug])) {
                    $image_url = $category_images[$cat->slug];
                }
            ?>
            <div class="category-card" onclick="loadSubCategories('<?php echo esc_js($cat->slug); ?>', <?php echo $cat->term_id; ?>, '<?php echo esc_js($cat->name); ?>')">
                <div class="category-image">
                    <?php if ($image_url): ?>
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($cat->name); ?>">
                    <?php else: ?>
                        <div style="font-size: 80px;"></div>
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
    function loadSubCategories(slug, parentId, parentName) {
        var display = document.getElementById('sub-categories-display');

        // Show loading
        display.innerHTML = '<div style="text-align: center; padding: 40px;"><p>Loading...</p></div>';

        // Make AJAX call to get sub-categories
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '<?php echo admin_url('admin-ajax.php'); ?>?action=get_sub_categories&parent_id=' + parentId + '&parent_slug=' + slug + '&parent_name=' + encodeURIComponent(parentName), true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                display.innerHTML = xhr.responseText;
                // Smooth scroll to sub-categories
                setTimeout(function() {
                    display.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            }
        };
        xhr.send();
    }

    function loadProductsByTag(tagSlug, tagName, subSlug, subName, catSlug, catName) {
        var display = document.getElementById('sub-categories-display');

        // Show loading
        display.innerHTML = '<div style="text-align: center; padding: 40px;"><p>Loading products...</p></div>';

        // Make AJAX call to get products
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '<?php echo admin_url('admin-ajax.php'); ?>?action=get_products_by_tag&tag_slug=' + tagSlug + '&tag_name=' + encodeURIComponent(tagName) + '&sub_slug=' + subSlug + '&sub_name=' + encodeURIComponent(subName) + '&cat_slug=' + catSlug + '&cat_name=' + encodeURIComponent(catName), true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                display.innerHTML = xhr.responseText;
                // Smooth scroll to products
                setTimeout(function() {
                    display.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            }
        };
        xhr.send();
    }

    function loadProductsBySubcategory(subSlug, subName, parentSlug, parentName) {
        var display = document.getElementById('sub-categories-display');

        // Show loading
        display.innerHTML = '<div style="text-align: center; padding: 40px;"><p>Loading products...</p></div>';

        // Make AJAX call to get products by subcategory
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '<?php echo admin_url('admin-ajax.php'); ?>?action=get_products_by_subcategory&sub_slug=' + subSlug + '&sub_name=' + encodeURIComponent(subName) + '&parent_slug=' + parentSlug + '&parent_name=' + encodeURIComponent(parentName), true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                display.innerHTML = xhr.responseText;
                // Smooth scroll to products
                setTimeout(function() {
                    display.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            }
        };
        xhr.send();
    }

    function loadTagsForSubcategory(subSlug, subName, parentSlug, parentName) {
        var display = document.getElementById('sub-categories-display');
        display.innerHTML = '<div style="text-align: center; padding: 40px;"><p>Loading...</p></div>';

        var xhr = new XMLHttpRequest();
        xhr.open('GET', '<?php echo admin_url('admin-ajax.php'); ?>?action=get_tags_for_subcat&sub_slug=' + subSlug + '&sub_name=' + encodeURIComponent(subName) + '&parent_slug=' + parentSlug + '&parent_name=' + encodeURIComponent(parentName), true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                display.innerHTML = xhr.responseText;
                setTimeout(function() { display.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 100);
            }
        };
        xhr.send();
    }

    function goBackToCategories() {
        document.getElementById('sub-categories-display').innerHTML = '';
        document.getElementById('sub-categories-display').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    </script>
    <?php
    return ob_get_clean();
}

// AJAX handler for sub-categories
add_action('wp_ajax_get_sub_categories', 'ajax_get_sub_categories');
add_action('wp_ajax_nopriv_get_sub_categories', 'ajax_get_sub_categories');
function ajax_get_sub_categories() {
    $parent_id = isset($_GET['parent_id']) ? intval($_GET['parent_id']) : 0;
    $parent_slug = isset($_GET['parent_slug']) ? sanitize_text_field($_GET['parent_slug']) : '';
    $parent_name = isset($_GET['parent_name']) ? sanitize_text_field($_GET['parent_name']) : '';

    // Get child categories - hide empty (0-product) subcategories
    $sub_cats = get_terms(array(
        'taxonomy' => 'product_cat',
        'parent' => $parent_id,
        'hide_empty' => true,
        'number' => 100
    ));

    // If there are child categories, display them
    if (!empty($sub_cats) && !is_wp_error($sub_cats)) {
        ?>
        <div class="breadcrumb-nav" style="margin-bottom: 20px;">
            <button onclick="goBackToCategories()" style="background: #32703B; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer;">← Back to Categories</button>
        </div>
        <div class="sub-category-grid">
            <?php foreach ($sub_cats as $sub):
                $thumbnail_id = get_term_meta($sub->term_id, 'thumbnail_id', true);
                $icon_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
                $product_count = $sub->count;
            ?>
            <div onclick="loadTagsForSubcategory('<?php echo esc_js($sub->slug); ?>', '<?php echo esc_js($sub->name); ?>', '<?php echo esc_js($parent_slug); ?>', '<?php echo esc_js($parent_name); ?>')" style="cursor: pointer;">
                <a href="javascript:void(0);" class="sub-category-card">
                    <div class="sub-category-icon">
                        <?php if ($icon_url): ?>
                            <img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($sub->name); ?>">
                        <?php else: ?>
                            <span style="font-size: 40px; color: #32703B;"></span>
                        <?php endif; ?>
                    </div>
                    <div class="sub-category-name"><?php echo esc_html($sub->name); ?></div>
                    <div class="sub-category-count"><?php echo intval($product_count); ?> products</div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
    } else {
        // No child categories - show products directly from this main category
        ?>
        <div class="breadcrumb-nav" style="margin-bottom: 20px;">
            <button onclick="goBackToCategories()" style="background: #32703B; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer;">← Back to Categories</button>
        </div>
        <div class="sub-categories-header">
            <h2><?php echo esc_html($parent_name); ?> - PRODUCTS</h2>
        </div>
        <?php
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 24,
            'paged' => 1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $parent_id,
                )
            )
        );
        $products = new WP_Query($args);

        echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">';
        if ($products->have_posts()) {
            while ($products->have_posts()) {
                $products->the_post();
                echo '<div style="border: 1px solid #e5e5e5; border-radius: 8px; padding: 15px; text-align: center;">';
                echo get_the_post_thumbnail(get_the_ID(), array(150, 150), array('style' => 'margin-bottom: 10px;'));
                echo '<h4 style="font-size: 14px; margin: 10px 0;">' . esc_html(get_the_title()) . '</h4>';
                echo '<a href="' . esc_url(get_permalink()) . '" style="background: #32703B; color: white; padding: 8px 12px; text-decoration: none; border-radius: 5px; display: inline-block;">View</a>';
                echo '</div>';
            }
        }
        echo '</div>';
        
        // Pagination
        if ($products->max_num_pages > 1) {
            echo '<div style="text-align: center; margin-top: 30px;">';
            echo paginate_links(array(
                'total' => $products->max_num_pages,
                'type' => 'list'
            ));
            echo '</div>';
        }
        wp_reset_postdata();
    }

    wp_die();
}
// AJAX handler: get tags for a sub-category (then clicking a tag will load products)
add_action('wp_ajax_get_tags_for_subcat', 'ajax_get_tags_for_subcat');
add_action('wp_ajax_nopriv_get_tags_for_subcat', 'ajax_get_tags_for_subcat');
function ajax_get_tags_for_subcat() {
    $sub_slug = isset($_GET['sub_slug']) ? sanitize_text_field($_GET['sub_slug']) : '';
    $sub_name = isset($_GET['sub_name']) ? sanitize_text_field($_GET['sub_name']) : '';
    $parent_slug = isset($_GET['parent_slug']) ? sanitize_text_field($_GET['parent_slug']) : '';
    $parent_name = isset($_GET['parent_name']) ? sanitize_text_field($_GET['parent_name']) : '';

    // Get parent category ID
    $parent_cat = get_term_by('slug', $parent_slug, 'product_cat');
    $parent_id = $parent_cat ? $parent_cat->term_id : 0;

    // Try to find the sub-category term
    $sub_term = get_term_by('slug', $sub_slug, 'product_cat');
    $term_id = $sub_term ? $sub_term->term_id : 0;

    // If no valid sub-term, fallback to showing products
    if (!$term_id) {
        // reuse existing products handler by delegating (call get_products_by_tag with empty tag)
        // Build minimal output: show products in parent or sub
        echo '<div class="sub-categories-header"><h2>' . esc_html($sub_name) . ' - PRODUCTS</h2></div>';
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 12,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => $sub_slug,
                )
            )
        );
        $products = new WP_Query($args);
        echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">';
        if ($products->have_posts()) {
            while ($products->have_posts()) { $products->the_post();
                echo '<div style="border: 1px solid #e5e5e5; border-radius: 8px; padding: 15px; text-align: center;">';
                echo get_the_post_thumbnail(get_the_ID(), array(150,150), array('style' => 'margin-bottom: 10px;'));
                echo '<h4 style="font-size: 14px; margin: 10px 0;">' . esc_html(get_the_title()) . '</h4>';
                echo '<a href="' . esc_url(get_permalink()) . '" style="background: #32703B; color: white; padding: 8px 12px; text-decoration: none; border-radius: 5px; display: inline-block;">View Product</a>';
                echo '</div>';
            }
        }
        echo '</div>';
        wp_reset_postdata();
        wp_die();
    }

    // Collect tags from products in this sub-category
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $term_id,
            )
        )
    );
    $products = new WP_Query($args);
    $all_tags = array();
    if ($products->have_posts()) {
        while ($products->have_posts()) {
            $products->the_post();
            $tags = get_the_terms(get_the_ID(), 'product_tag');
            if ($tags && !is_wp_error($tags)) {
                foreach ($tags as $tag) {
                    if (!isset($all_tags[$tag->term_id])) {
                        $all_tags[$tag->term_id] = array('name' => $tag->name, 'slug' => $tag->slug, 'count' => 0);
                    }
                    $all_tags[$tag->term_id]['count']++;
                }
            }
        }
        wp_reset_postdata();
    }

    // Filter fence-style tags (reuse same list)
    $fence_styles = array('2-rail', '3-rail', '4-rail', 'picket', 'privacy', 'pool', 'lattice', 'smooth-top', 'square-top', 'point-top', 'curved-top', 'scalloped-picket', 'pipe', 'post', 'cap', 'fitting', 'tools', 'misc');

    $filtered = array();
    foreach ($all_tags as $t) {
        $slug_lower = strtolower($t['slug']);
        foreach ($fence_styles as $style) {
            if (strpos($slug_lower, $style) !== false) { $filtered[$t['slug']] = $t; break; }
        }
    }

    if (empty($filtered)) {
        // No tags — show products instead
        echo '<div class="sub-categories-header"><h2>' . esc_html($sub_name) . ' - PRODUCTS</h2></div>';
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 12,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $term_id,
                )
            )
        );
        $products = new WP_Query($args);
        echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">';
        if ($products->have_posts()) {
            while ($products->have_posts()) { $products->the_post();
                echo '<div style="border: 1px solid #e5e5e5; border-radius: 8px; padding: 15px; text-align: center;">';
                echo get_the_post_thumbnail(get_the_ID(), array(150,150), array('style' => 'margin-bottom: 10px;'));
                echo '<h4 style="font-size: 14px; margin: 10px 0;">' . esc_html(get_the_title()) . '</h4>';
                echo '<a href="' . esc_url(get_permalink()) . '" style="background: #32703B; color: white; padding: 8px 12px; text-decoration: none; border-radius: 5px; display: inline-block;">View Product</a>';
                echo '</div>';
            }
        }
        echo '</div>';
        wp_reset_postdata();
        wp_die();
    }

    // Render tag cards
    ?>
    <div class="breadcrumb-nav" style="margin-bottom: 20px;">
        <button onclick="loadSubCategories('<?php echo esc_js($parent_slug); ?>', <?php echo intval($parent_id); ?>, '<?php echo esc_js($parent_name); ?>')" style="background: #32703B; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer;">← Back to <?php echo esc_html($parent_name); ?></button>
    </div>
    <div class="sub-categories-header">
        <h2><?php echo esc_html($parent_name); ?> / <?php echo esc_html($sub_name); ?> - TAGS</h2>
        <p>Select a tag to view products</p>
    </div>
    <div class="sub-category-grid">
        <?php foreach ($filtered as $tag): ?>
            <div style="cursor: pointer;" onclick="loadProductsByTag('<?php echo esc_js($tag['slug']); ?>', '<?php echo esc_js($tag['name']); ?>', '<?php echo esc_js($sub_slug); ?>', '<?php echo esc_js($sub_name); ?>', '<?php echo esc_js($parent_slug); ?>', '<?php echo esc_js($parent_name); ?>')">
                <a href="javascript:void(0);" class="sub-category-card">
                    <div class="sub-category-icon"><span style="font-size: 40px; color: #32703B;"></span></div>
                    <div class="sub-category-name"><?php echo esc_html($tag['name']); ?></div>
                    <div class="sub-category-count"><?php echo intval($tag['count']); ?> products</div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    wp_die();
}

// AJAX handler for products by tag/sub-category
add_action('wp_ajax_get_products_by_tag', 'ajax_get_products_by_tag');
add_action('wp_ajax_nopriv_get_products_by_tag', 'ajax_get_products_by_tag');
function ajax_get_products_by_tag() {
    $tag_slug = isset($_GET['tag_slug']) ? sanitize_text_field($_GET['tag_slug']) : '';
    $tag_name = isset($_GET['tag_name']) ? sanitize_text_field($_GET['tag_name']) : '';
    $sub_slug = isset($_GET['sub_slug']) ? sanitize_text_field($_GET['sub_slug']) : '';
    $sub_name = isset($_GET['sub_name']) ? sanitize_text_field($_GET['sub_name']) : '';
    $cat_slug = isset($_GET['cat_slug']) ? sanitize_text_field($_GET['cat_slug']) : '';
    $cat_name = isset($_GET['cat_name']) ? sanitize_text_field($_GET['cat_name']) : '';

    // Get the parent category ID from slug
    $parent_cat = get_term_by('slug', $cat_slug, 'product_cat');
    $parent_id = $parent_cat ? $parent_cat->term_id : 0;

    // Get the subcategory ID
    $sub_cat = get_term_by('slug', $sub_slug, 'product_cat');
    $sub_cat_id = $sub_cat ? $sub_cat->term_id : 0;

    // Get the tag
    $tag_term = get_term_by('slug', $tag_slug, 'product_tag');
    $tag_id = $tag_term ? $tag_term->term_id : 0;

    ?>
    <div class="breadcrumb-nav" style="margin-bottom: 20px;">
        <button onclick="loadTagsForSubcategory('<?php echo esc_js($sub_slug); ?>', '<?php echo esc_js($sub_name); ?>', '<?php echo esc_js($cat_slug); ?>', '<?php echo esc_js($cat_name); ?>')" style="background: #32703B; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer;">← Back to <?php echo esc_html($sub_name); ?></button>
    </div>
    <div class="sub-categories-header">
        <h2><?php echo esc_html($cat_name); ?> / <?php echo esc_html($sub_name); ?> / <?php echo esc_html($tag_name); ?></h2>
        <p>Showing products in <?php echo esc_html($tag_name); ?></p>
    </div>

    <?php
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 20,
        'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
        'tax_query' => array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'product_tag',
                'field' => 'term_id',
                'terms' => $tag_id,
            ),
            array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $sub_cat_id,
            )
        )
    );

    $products = new WP_Query($args);

    echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">';
    if ($products->have_posts()) {
        while ($products->have_posts()) {
            $products->the_post();
            echo '<div style="border: 1px solid #e5e5e5; border-radius: 8px; padding: 15px; text-align: center;">';
            echo get_the_post_thumbnail(get_the_ID(), array(150, 150), array('style' => 'margin-bottom: 10px;'));
            echo '<h4 style="font-size: 14px; margin: 10px 0;">' . esc_html(get_the_title()) . '</h4>';
            echo '<p style="font-size: 12px; color: #666; margin: 5px 0;">' . esc_html(get_the_excerpt()) . '</p>';
            echo '<a href="' . esc_url(get_permalink()) . '" style="background: #32703B; color: white; padding: 8px 12px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;">View Product</a>';
            echo '</div>';
        }
    } else {
        echo '<p style="grid-column: 1/-1; text-align: center; padding: 40px;">No products found in this category.</p>';
    }
    echo '</div>';

    // Pagination
    if ($products->max_num_pages > 1) {
        echo '<div style="text-align: center; margin-top: 30px;">';
        echo paginate_links(array(
            'total' => $products->max_num_pages,
            'current' => max(1, get_query_var('paged')),
            'type' => 'list'
        ));
        echo '</div>';
    }

    wp_reset_postdata();
    wp_die();
}

// AJAX handler for products by subcategory
add_action('wp_ajax_get_products_by_subcategory', 'ajax_get_products_by_subcategory');
add_action('wp_ajax_nopriv_get_products_by_subcategory', 'ajax_get_products_by_subcategory');
function ajax_get_products_by_subcategory() {
    $sub_slug = isset($_GET['sub_slug']) ? sanitize_text_field($_GET['sub_slug']) : '';
    $sub_name = isset($_GET['sub_name']) ? sanitize_text_field($_GET['sub_name']) : '';
    $parent_slug = isset($_GET['parent_slug']) ? sanitize_text_field($_GET['parent_slug']) : '';
    $parent_name = isset($_GET['parent_name']) ? sanitize_text_field($_GET['parent_name']) : '';

    // Get the parent category ID from slug
    $parent_cat = get_term_by('slug', $parent_slug, 'product_cat');
    $parent_id = $parent_cat ? $parent_cat->term_id : 0;

    // Get the sub-category
    $sub_term = get_term_by('slug', $sub_slug, 'product_cat');
    $sub_id = $sub_term ? $sub_term->term_id : 0;

    ?>
    <div class="breadcrumb-nav" style="margin-bottom: 20px;">
        <button onclick="loadSubCategories('<?php echo esc_js($parent_slug); ?>', <?php echo intval($parent_id); ?>, '<?php echo esc_js($parent_name); ?>')" style="background: #32703B; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer;">← Back to <?php echo esc_html($parent_name); ?></button>
    </div>
    <div class="sub-categories-header">
        <h2><?php echo esc_html($parent_name); ?> / <?php echo esc_html($sub_name); ?></h2>
        <p>Showing all <?php echo esc_html($sub_name); ?> products</p>
    </div>

    <?php
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 24,
        'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $sub_id,
            )
        )
    );

    $products = new WP_Query($args);

    echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">';
    if ($products->have_posts()) {
        while ($products->have_posts()) {
            $products->the_post();
            echo '<div style="border: 1px solid #e5e5e5; border-radius: 8px; padding: 15px; text-align: center;">';
            echo get_the_post_thumbnail(get_the_ID(), array(150, 150), array('style' => 'margin-bottom: 10px;'));
            echo '<h4 style="font-size: 14px; margin: 10px 0;">' . esc_html(get_the_title()) . '</h4>';
            echo '<p style="font-size: 12px; color: #666; margin: 5px 0;">' . esc_html(wp_trim_words(get_the_excerpt(), 10)) . '</p>';
            echo '<a href="' . esc_url(get_permalink()) . '" style="background: #32703B; color: white; padding: 8px 12px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;">View Product</a>';
            echo '</div>';
        }
    } else {
        echo '<p style="grid-column: 1/-1; text-align: center; padding: 40px;">No products found in this category.</p>';
    }
    echo '</div>';

    // Pagination
    if ($products->max_num_pages > 1) {
        echo '<div style="text-align: center; margin-top: 30px;">';
        echo paginate_links(array(
            'total' => $products->max_num_pages,
            'current' => max(1, get_query_var('paged')),
            'type' => 'list'
        ));
        echo '</div>';
    }

    wp_reset_postdata();
    wp_die();
}
?>
