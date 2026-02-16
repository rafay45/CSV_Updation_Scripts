<?php
/**
 * taxonomy-product_cat.php
 * Place this file in: wp-content/themes/YOUR-THEME-NAME/taxonomy-product_cat.php
 *
 * Handles all WooCommerce product category pages with custom design.
 * Logic:
 *   Level 1 (Vinyl)              → show subcategory cards
 *   Level 2 (Privacy)            → show sub-subcategory cards (or tags if no children)
 *   Level 3 (Tools/Misc)         → show tag cards
 *   Level 3 + ?tag=top-rail      → show products with filter sidebar
 */

get_header();

// ── Current category ─────────────────────────────────────────────────────────
$current_cat = get_queried_object();
$cat_id      = $current_cat->term_id;
$cat_name    = $current_cat->name;
$cat_slug    = $current_cat->slug;
$cat_depth   = dpc_get_cat_depth($current_cat); // 0 = top-level

// ── Ancestors (breadcrumb) ────────────────────────────────────────────────────
$ancestors = array_reverse(get_ancestors($cat_id, 'product_cat'));

// ── Tag filter from URL ───────────────────────────────────────────────────────
$active_tag_slug = isset($_GET['tag']) ? sanitize_text_field($_GET['tag']) : '';
$active_tag      = $active_tag_slug ? get_term_by('slug', $active_tag_slug, 'product_tag') : null;

// ── Helper: get category depth ────────────────────────────────────────────────
function dpc_get_cat_depth($term) {
    $depth = 0;
    $t = $term;
    while ($t->parent != 0) {
        $depth++;
        $t = get_term($t->parent, 'product_cat');
        if (is_wp_error($t)) break;
    }
    return $depth;
}

// ── Helper: get product count including children ──────────────────────────────
function dpc_count_products($term_id) {
    $ids = array($term_id);
    $children = get_term_children($term_id, 'product_cat');
    if (!is_wp_error($children)) $ids = array_merge($ids, $children);
    $q = new WP_Query(array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'tax_query'      => array(array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $ids,
        )),
    ));
    return $q->found_posts;
}

// ── Child categories ──────────────────────────────────────────────────────────
$child_cats = get_terms(array(
    'taxonomy'   => 'product_cat',
    'parent'     => $cat_id,
    'hide_empty' => true,
));

// ── Tags for this category (used at level 3) ──────────────────────────────────
function dpc_get_tags_for_cat($cat_id) {
    $ids = array($cat_id);
    $ch  = get_term_children($cat_id, 'product_cat');
    if (!is_wp_error($ch)) $ids = array_merge($ids, $ch);

    $q = new WP_Query(array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'tax_query'      => array(array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $ids,
        )),
    ));

    $tags = array();
    foreach ($q->posts as $pid) {
        $product_tags = get_the_terms($pid, 'product_tag');
        if ($product_tags && !is_wp_error($product_tags)) {
            foreach ($product_tags as $tag) {
                if (!isset($tags[$tag->term_id])) {
                    $tags[$tag->term_id] = array(
                        'name'  => $tag->name,
                        'slug'  => $tag->slug,
                        'count' => 0,
                    );
                }
                $tags[$tag->term_id]['count']++;
            }
        }
    }
    usort($tags, function($a, $b) { return strcmp($a['name'], $b['name']); });
    return $tags;
}

// ── Decide what to show ───────────────────────────────────────────────────────
// has_children  = show subcategory cards
// no children + no tag filter = show tag cards
// no children + tag filter    = show products
$has_children = !empty($child_cats) && !is_wp_error($child_cats);
$show_mode    = 'subcategories'; // default

if ($has_children) {
    $show_mode = 'subcategories';
} elseif ($active_tag_slug) {
    $show_mode = 'products';
} else {
    $show_mode = 'tags';
}
?>

<style>
/* ── Wrapper ── */
.dpc-cat-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px 20px;
}
/* ── Breadcrumb ── */
.dpc-breadcrumb {
    font-size: 13px;
    color: #888;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
}
.dpc-breadcrumb a {
    color: #32703B;
    text-decoration: none;
    font-weight: 500;
}
.dpc-breadcrumb a:hover { text-decoration: underline; }
.dpc-breadcrumb span { color: #ccc; }
/* ── Page heading ── */
.dpc-cat-heading {
    font-size: 30px;
    font-weight: 700;
    color: #222;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 30px;
}
/* ── Category / Tag card grid ── */
.dpc-card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}
.dpc-cat-card {
    background: #fff;
    border: 2px solid #e5e5e5;
    border-radius: 16px;
    padding: 30px 16px 40px;
    text-align: center;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    transition: all 0.25s ease;
    min-height: 160px;
    overflow: visible;
}
.dpc-cat-card:hover {
    border-color: #32703B;
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}
.dpc-cat-card .dpc-card-icon {
    width: 90px;
    height: 90px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
}
.dpc-cat-card .dpc-card-icon img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}
.dpc-cat-card .dpc-card-label {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 20px;
    padding: 8px 16px;
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translate(-50%, 50%);
    white-space: nowrap;
    min-width: 110px;
}
.dpc-cat-card .dpc-card-count {
    font-size: 11px;
    color: #aaa;
    margin-top: 4px;
}
/* Active tag card */
.dpc-cat-card.active-tag {
    border-color: #32703B;
    background: #f0f9f1;
}
/* ── Products layout ── */
.dpc-products-layout {
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 30px;
}
@media (max-width: 768px) {
    .dpc-products-layout { grid-template-columns: 1fr; }
    .dpc-filters-sidebar { display: none; }
    .dpc-card-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); }
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
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    color: #333;
    margin: 0 0 14px;
    padding-bottom: 10px;
    border-bottom: 2px solid #32703B;
}
.dpc-filter-group {
    margin-bottom: 16px;
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 12px;
}
.dpc-filter-group:last-child { border-bottom: none; margin-bottom: 0; }
.dpc-filter-group h4 {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: #444;
    margin: 0 0 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.dpc-filter-group h4::after { content: '▾'; font-size: 13px; color: #888; }
.dpc-filter-group ul { list-style: none; margin: 0; padding: 0; }
.dpc-filter-group ul li { margin-bottom: 6px; }
.dpc-filter-group ul li label {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 12px;
    color: #333;
    cursor: pointer;
    line-height: 1.4;
}
.dpc-filter-group ul li label input[type="checkbox"] {
    cursor: pointer;
    accent-color: #32703B;
    width: 13px;
    height: 13px;
    flex-shrink: 0;
}
.dpc-filter-group ul li label .dpc-fc { margin-left: auto; color: #999; font-size: 11px; }
/* ── Products grid ── */
.dpc-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
    gap: 16px;
}
.dpc-product-card {
    border: 1px solid #dde3ec;
    border-radius: 6px;
    background: #fff;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: box-shadow 0.2s ease;
}
.dpc-product-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.12); }
.dpc-product-card .dpc-img-wrap {
    display: block;
    background: #f5f7fa;
    overflow: hidden;
    aspect-ratio: 1/1;
    text-decoration: none;
}
.dpc-product-card .dpc-img-wrap img {
    width: 100%; height: 100%;
    object-fit: cover; display: block;
    transition: transform 0.3s ease;
}
.dpc-product-card:hover .dpc-img-wrap img { transform: scale(1.04); }
.dpc-product-card .dpc-card-body {
    padding: 10px 12px 12px;
    display: flex; flex-direction: column; flex-grow: 1;
}
.dpc-product-card h4 {
    font-size: 12px; line-height: 1.45; color: #222;
    margin: 0 0 8px; flex-grow: 1;
}
.dpc-product-card h4 a { color: #222; text-decoration: none; }
.dpc-product-card h4 a:hover { color: #32703B; }
.dpc-product-card .dpc-price {
    font-size: 15px; font-weight: 700;
    color: #32703B; margin-bottom: 10px;
}
.dpc-add-to-cart-btn {
    display: flex; align-items: center; justify-content: center;
    gap: 6px; width: 100%; background: #32703B; color: white;
    border: none; padding: 9px 8px; border-radius: 4px;
    font-size: 12px; font-weight: 600; cursor: pointer;
    text-decoration: none; transition: background 0.2s ease;
    text-align: center;
}
.dpc-add-to-cart-btn:hover { background: #2a5330; color: white; }
.dpc-sort-bar {
    display: flex; align-items: center; justify-content: flex-end;
    margin-bottom: 14px; gap: 10px; font-size: 13px; color: #555;
}
.dpc-sort-bar select {
    padding: 5px 10px; border: 1px solid #ccc;
    border-radius: 4px; font-size: 13px; cursor: pointer;
}
.dpc-no-results {
    grid-column: 1/-1; text-align: center;
    padding: 60px 20px; color: #888; font-size: 15px;
}
/* ── Tag filter bar (above products) ── */
.dpc-tag-filter-bar {
    display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px;
}
.dpc-tag-pill {
    background: #f0f0f0; color: #333; border: 1px solid #ddd;
    border-radius: 20px; padding: 6px 14px; font-size: 12px;
    font-weight: 500; text-decoration: none; transition: all 0.2s ease;
    white-space: nowrap;
}
.dpc-tag-pill:hover, .dpc-tag-pill.active {
    background: #32703B; color: white; border-color: #32703B;
}
</style>

<div class="dpc-cat-page">

    <?php
    // ── Breadcrumb ──────────────────────────────────────────────────────────
    $shop_url = get_permalink(wc_get_page_id('shop'));
    ?>
    <nav class="dpc-breadcrumb">
        <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
        <span>›</span>
        <a href="<?php echo esc_url($shop_url); ?>">Shop</a>
        <?php foreach ($ancestors as $anc_id):
            $anc = get_term($anc_id, 'product_cat');
            if (!is_wp_error($anc)):
        ?>
            <span>›</span>
            <a href="<?php echo esc_url(get_term_link($anc)); ?>"><?php echo esc_html($anc->name); ?></a>
        <?php endif; endforeach; ?>
        <span>›</span>
        <strong><?php echo esc_html($cat_name); ?></strong>
        <?php if ($active_tag): ?>
            <span>›</span>
            <strong><?php echo esc_html($active_tag->name); ?></strong>
        <?php endif; ?>
    </nav>

    <?php
    // ════════════════════════════════════════════════════════════════════════
    // MODE 1: Show child category cards
    // ════════════════════════════════════════════════════════════════════════
    if ($show_mode === 'subcategories'):
    ?>
        <h1 class="dpc-cat-heading"><?php echo esc_html($cat_name); ?></h1>
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
                        <span style="font-size:50px;">📦</span>
                    <?php endif; ?>
                </div>
                <div class="dpc-card-count"><?php echo intval($count); ?> products</div>
                <div class="dpc-card-label"><?php echo esc_html($child->name); ?></div>
            </a>
            <?php endforeach; ?>
        </div>

    <?php
    // ════════════════════════════════════════════════════════════════════════
    // MODE 2: Show tag cards (no children)
    // ════════════════════════════════════════════════════════════════════════
    elseif ($show_mode === 'tags'):
        $tags = dpc_get_tags_for_cat($cat_id);
    ?>
        <h1 class="dpc-cat-heading"><?php echo esc_html($cat_name); ?></h1>

        <?php if (empty($tags)): ?>
            <?php
            // No tags either - show products directly
            $show_mode = 'products';
            ?>
        <?php else: ?>
            <div class="dpc-card-grid">
                <?php foreach ($tags as $tag):
                    $cat_url  = get_term_link($current_cat);
                    $tag_url  = add_query_arg('tag', $tag['slug'], $cat_url);
                ?>
                <a href="<?php echo esc_url($tag_url); ?>" class="dpc-cat-card">
                    <div class="dpc-card-icon">
                        <span style="font-size:50px;">🏷️</span>
                    </div>
                    <div class="dpc-card-count"><?php echo intval($tag['count']); ?> products</div>
                    <div class="dpc-card-label"><?php echo esc_html($tag['name']); ?></div>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>

    <?php
    // ════════════════════════════════════════════════════════════════════════
    // MODE 3: Show products (with tag filter)
    // ════════════════════════════════════════════════════════════════════════
    if ($show_mode === 'products'):

        // Build tax query
        $tax_query = array(
            'relation' => 'AND',
            array(
                'taxonomy'         => 'product_cat',
                'field'            => 'term_id',
                'terms'            => $cat_id,
                'include_children' => true,
            ),
        );
        if ($active_tag_slug) {
            $tax_query[] = array(
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => $active_tag_slug,
            );
        }

        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => 500,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'tax_query'      => $tax_query,
        );
        $query = new WP_Query($args);

        // Collect attribute data + products
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
        ksort($attr_data);
        foreach ($attr_data as &$vals) ksort($vals);
        unset($vals);

        // Tag filter pills (all tags for this category)
        $all_tags_for_cat = dpc_get_tags_for_cat($cat_id);
        $cat_base_url     = get_term_link($current_cat);
    ?>
        <h1 class="dpc-cat-heading">
            <?php echo esc_html($cat_name); ?>
            <?php if ($active_tag): ?> — <?php echo esc_html($active_tag->name); ?><?php endif; ?>
        </h1>

        <?php if (!empty($all_tags_for_cat)): ?>
        <div class="dpc-tag-filter-bar">
            <a href="<?php echo esc_url($cat_base_url); ?>" class="dpc-tag-pill <?php echo !$active_tag_slug ? 'active' : ''; ?>">All</a>
            <?php foreach ($all_tags_for_cat as $t): ?>
            <a href="<?php echo esc_url(add_query_arg('tag', $t['slug'], $cat_base_url)); ?>"
               class="dpc-tag-pill <?php echo ($active_tag_slug === $t['slug']) ? 'active' : ''; ?>">
                <?php echo esc_html($t['name']); ?>
                <span style="opacity:0.7">(<?php echo intval($t['count']); ?>)</span>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="dpc-products-layout">

            <!-- LEFT: Filters sidebar -->
            <aside class="dpc-filters-sidebar">
                <h3>Filters</h3>
                <?php if (empty($attr_data)): ?>
                    <p style="font-size:12px;color:#888;">No filters available.</p>
                <?php else: ?>
                    <?php foreach ($attr_data as $label => $values):
                        $safe = esc_attr(strtolower(str_replace(' ', '_', $label)));
                    ?>
                    <div class="dpc-filter-group">
                        <h4><?php echo esc_html($label); ?></h4>
                        <ul>
                            <?php foreach ($values as $val => $cnt):
                                $cb_id = 'dpc_' . $safe . '_' . md5($val);
                            ?>
                            <li>
                                <label for="<?php echo $cb_id; ?>">
                                    <input type="checkbox" id="<?php echo $cb_id; ?>"
                                           class="dpc-filter-cb"
                                           data-attr="<?php echo esc_attr($label); ?>"
                                           data-val="<?php echo esc_attr($val); ?>">
                                    <?php echo esc_html($val); ?>
                                    <span class="dpc-fc">(<?php echo intval($cnt); ?>)</span>
                                </label>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </aside>

            <!-- RIGHT: Products -->
            <div>
                <div class="dpc-sort-bar">
                    Sort By |
                    <select onchange="dpcSort(this.value)">
                        <option value="default">Best Match</option>
                        <option value="price_asc">Price: Low to High</option>
                        <option value="price_desc">Price: High to Low</option>
                        <option value="title_asc">Name: A-Z</option>
                    </select>
                </div>

                <div class="dpc-products-grid" id="dpc-grid">
                    <?php if (empty($all_products)): ?>
                        <p class="dpc-no-results">No products found.</p>
                    <?php else: ?>
                        <?php foreach ($all_products as $p):
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
                        <?php endforeach; ?>
                        <p class="dpc-no-results" style="display:none;">No products match your filters.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div><!-- .dpc-products-layout -->

    <?php endif; // end MODE 3 ?>

</div><!-- .dpc-cat-page -->

<script>
// ── Client-side attribute filtering ──────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.dpc-filter-cb').forEach(function(cb) {
        cb.addEventListener('change', dpcApplyFilters);
    });
});

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
        if (Object.keys(active).length === 0) { card.style.display = ''; return; }
        var attrs = JSON.parse(card.dataset.attrs || '{}');
        var show  = true;
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
        var noRes   = grid.querySelector('.dpc-no-results');
        if (noRes) noRes.style.display = visible === 0 ? '' : 'none';
    }
}

// ── Sort ──────────────────────────────────────────────────────────────────────
function dpcSort(val) {
    var grid  = document.getElementById('dpc-grid');
    if (!grid) return;
    var cards = Array.from(grid.querySelectorAll('.dpc-product-card'));
    cards.sort(function(a, b) {
        if (val === 'title_asc') return a.dataset.title.localeCompare(b.dataset.title);
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

<?php get_footer(); ?>
