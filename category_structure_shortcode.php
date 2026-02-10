<?php
/**
 * WordPress Category Structure Display
 * اسے wp-admin میں Custom Admin Page کے طور پر شامل کریں
 * یا shortcode کے طور پر استعمال کریں: [show_category_structure]
 */

// Shortcode کے طور پر استعمال کریں
add_shortcode('show_category_structure', 'display_category_hierarchy');

function display_category_hierarchy() {
    ob_start();
    
    $main_category_slugs = array('vinyl', 'ornamental', 'chain-link', 'simtek', 'modern', 'trex', 'wood', 'agriculture', 'metal-horse-fence');
    
    ?>
    <div style="background: #f5f5f5; padding: 30px; border-radius: 10px; font-family: monospace; max-width: 1000px; margin: 20px auto;">
        <h2 style="text-align: center; color: #32703B; margin-bottom: 30px; font-size: 24px;">
            📊 WordPress Category Structure
        </h2>
        
        <?php
        foreach ($main_category_slugs as $slug) {
            $parent_cat = get_term_by('slug', $slug, 'product_cat');
            
            if ($parent_cat && !is_wp_error($parent_cat)) {
                ?>
                <div style="background: white; padding: 20px; margin-bottom: 20px; border-left: 5px solid #32703B; border-radius: 5px;">
                    <!-- Main Category -->
                    <h3 style="color: #32703B; margin: 0 0 15px 0; font-size: 18px;">
                        📁 <?php echo strtoupper($parent_cat->name); ?>
                    </h3>
                    
                    <div style="background: #f9f9f9; padding: 12px; border-radius: 5px; margin-bottom: 15px; font-size: 12px; color: #666;">
                        <strong>Category ID:</strong> <?php echo $parent_cat->term_id; ?> | 
                        <strong>Slug:</strong> <?php echo $parent_cat->slug; ?> | 
                        <strong>Total Products:</strong> <?php echo $parent_cat->count; ?>
                    </div>
                    
                    <!-- Subcategories -->
                    <?php
                    $sub_cats = get_terms(array(
                        'taxonomy' => 'product_cat',
                        'parent' => $parent_cat->term_id,
                        'hide_empty' => false,
                        'number' => 100
                    ));
                    
                    if (!empty($sub_cats) && !is_wp_error($sub_cats)) {
                        ?>
                        <div style="margin-left: 20px;">
                            <strong style="color: #555; display: block; margin-bottom: 10px;">
                                ➜ Sub-Categories: (<?php echo count($sub_cats); ?>)
                            </strong>
                            
                            <?php
                            foreach ($sub_cats as $index => $sub) {
                                $is_last = ($index === count($sub_cats) - 1);
                                ?>
                                <div style="margin-bottom: 8px; padding-left: 15px; border-left: 2px solid #ddd; <?php echo $is_last ? 'border-left-color: #ddd;' : ''; ?>">
                                    <span style="color: #32703B; font-weight: bold;">└─ <?php echo $sub->name; ?></span>
                                    <div style="font-size: 11px; color: #999; margin-top: 3px;">
                                        ID: <?php echo $sub->term_id; ?> | 
                                        Slug: <?php echo $sub->slug; ?> | 
                                        Products: <?php echo $sub->count; ?>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div style="color: #e74c3c; font-style: italic; margin-left: 20px;">
                            ❌ کوئی Subcategories نہیں
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
        }
        ?>
        
        <div style="background: #e8f5e9; padding: 15px; border-radius: 5px; margin-top: 20px; color: #2e7d32;">
            <strong>✅ Category Structure Display مکمل</strong>
            <p style="margin: 10px 0 0 0; font-size: 12px;">
                یہ structure آپ کی WordPress میں موجود ہے۔ اگر کوئی category یا subcategory نہیں ہے تو وہ ابھی WordPress میں بنانی ہے۔
            </p>
        </div>
    </div>
    
    <?php
    return ob_get_clean();
}

// یا Admin Menu میں شامل کریں
add_action('admin_menu', 'add_category_structure_menu');

function add_category_structure_menu() {
    add_menu_page(
        'Category Structure',
        'Fence Categories',
        'manage_options',
        'fence-category-structure',
        'render_category_structure_page',
        'dashicons-folder-open',
        20
    );
}

function render_category_structure_page() {
    echo '<div class="wrap">';
    echo do_shortcode('[show_category_structure]');
    echo '</div>';
}
?>
