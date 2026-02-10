<?php
/**
 * WordPress Category Structure Debugger
 * یہ script تمام Main Categories اور ان کی Subcategories دکھاتا ہے
 */

// WordPress ko load کریں
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

// Main categories slugs
$main_category_slugs = array('vinyl', 'ornamental', 'chain-link', 'simtek', 'modern', 'trex', 'wood', 'agriculture', 'metal-horse-fence');

echo "========================================\n";
echo "WORDPRESS CATEGORY HIERARCHY\n";
echo "========================================\n\n";

// ہر main category کے لیے
foreach ($main_category_slugs as $slug) {
    // Main category تلاش کریں
    $parent_cat = get_term_by('slug', $slug, 'product_cat');
    
    if ($parent_cat && !is_wp_error($parent_cat)) {
        echo "📁 MAIN CATEGORY: " . strtoupper($parent_cat->name) . "\n";
        echo "   ID: " . $parent_cat->term_id . " | Slug: " . $parent_cat->slug . "\n";
        echo "   Total Products: " . $parent_cat->count . "\n";
        echo "   ─────────────────────────────────────\n";
        
        // اس category کی subcategories تلاش کریں
        $sub_cats = get_terms(array(
            'taxonomy' => 'product_cat',
            'parent' => $parent_cat->term_id,
            'hide_empty' => false,
            'number' => 100
        ));
        
        if (!empty($sub_cats) && !is_wp_error($sub_cats)) {
            echo "   ➜ SUB-CATEGORIES: (" . count($sub_cats) . ")\n";
            foreach ($sub_cats as $sub) {
                echo "      └─ " . $sub->name . "\n";
                echo "         (ID: " . $sub->term_id . " | Slug: " . $sub->slug . " | Products: " . $sub->count . ")\n";
            }
        } else {
            echo "   ❌ کوئی Subcategories نہیں\n";
        }
        
        echo "\n";
    }
}

echo "========================================\n";
echo "✅ Category Structure Display مکمل\n";
echo "========================================\n";
?>
