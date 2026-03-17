// ==========================================
// Per Foot Pricing - FORCED VERSION
// ==========================================

if (!function_exists('pfp_get_product_length')) {
    function pfp_get_product_length($product) {
        if (!$product) return 1;
        $attributes = $product->get_attributes();
        foreach ($attributes as $attr_name => $attr_obj) {
            $label = strtolower(wc_attribute_label($attr_name));
            if ($label === 'length') {
                if (is_object($attr_obj) && method_exists($attr_obj, 'is_taxonomy')) {
                    if ($attr_obj->is_taxonomy()) {
                        $terms = wp_get_post_terms($product->get_id(), $attr_name, array('fields' => 'names'));
                        if (!is_wp_error($terms) && !empty($terms)) {
                            preg_match('/[\d.]+/', $terms[0], $matches);
                            if (!empty($matches)) return floatval($matches[0]);
                        }
                    } else {
                        $options = $attr_obj->get_options();
                        if (!empty($options)) {
                            $val = is_array($options) ? $options[0] : $options;
                            preg_match('/[\d.]+/', $val, $matches);
                            if (!empty($matches)) return floatval($matches[0]);
                        }
                    }
                }
            }
        }
        return 1;
    }
}

// Price multiply (working)
if (!function_exists('pfp_modify_price_display')) {
    add_filter('woocommerce_get_price_html', 'pfp_modify_price_display', 10, 2);
    function pfp_modify_price_display($price_html, $product) {
        if (!$product || $product->get_type() === 'variable') return $price_html;
        $length = pfp_get_product_length($product);
        if ($length <= 1) return $price_html;
        $base_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        if (!$base_price) return $price_html;
        $total_regular = floatval($base_price) * $length;
        $total_sale = $sale_price ? floatval($sale_price) * $length : null;
        if ($total_sale) {
            return '<del>' . wc_price($total_regular) . '</del> <ins>' . wc_price($total_sale) . '</ins>';
        }
        return wc_price($total_regular);
    }
}

// FORCE render using wp_footer for single product pages
if (!function_exists('pfp_force_render_boxes')) {
    add_action('wp_footer', 'pfp_force_render_boxes');
    function pfp_force_render_boxes() {
        if (!is_product()) return;

        global $product;
        if (!$product || $product->get_type() === 'variable') return;

        $length = pfp_get_product_length($product);
        if ($length <= 1) return;

        $base_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        if (!$base_price) return;

        $per_foot_price = $sale_price ? $sale_price : $base_price;
        ?>
        <script>
        jQuery(document).ready(function($) {
            // Find the price location
            var priceContainer = $('.product .price').first().parent();

            // Add Green Box - Per Foot Price
            var greenBox = '<div class="pfp-per-foot-price" style="background: #f0f9f1; border: 2px solid #327A1F; border-radius: 8px; padding: 15px; margin: 20px 0; text-align: center;">' +
                '<div style="font-size: 12px; color: #666; font-weight: 600; text-transform: uppercase; margin-bottom: 5px;">Price Per Foot</div>' +
                '<div style="font-size: 24px; font-weight: 700; color: #327A1F;"><?php echo wc_price($per_foot_price); ?> / ft</div>' +
                '<div style="font-size: 11px; color: #888; margin-top: 5px;">Total Length: <?php echo $length; ?> feet</div>' +
            '</div>';

            // Add Yellow Notice - Above Add to Cart
            var yellowNotice = '<div class="pfp-notice" style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 12px; margin-bottom: 15px; font-size: 13px; color: #856404;">' +
                '<strong>⚠️ Note:</strong> This product is sold as a complete piece of <?php echo $length; ?> feet. Quantity of 1 = <?php echo $length; ?> feet.' +
            '</div>';

            // Insert green box after price
            if (!$('.pfp-per-foot-price').length) {
                priceContainer.after(greenBox);
            }

            // Insert yellow notice before add to cart button
            var cartButton = $('button[name="add-to-cart"], .single_add_to_cart_button').first();
            if (cartButton.length && !$('.pfp-notice').length) {
                cartButton.before(yellowNotice);
            }
        });
        </script>
        <?php
    }
}

// Cart price update (working)
if (!function_exists('pfp_update_cart_price')) {
    add_action('woocommerce_before_calculate_totals', 'pfp_update_cart_price', 10, 1);
    function pfp_update_cart_price($cart) {
        if (is_admin() && !defined('DOING_AJAX')) return;
        foreach ($cart->get_cart() as $cart_item) {
            $product = $cart_item['data'];
            if (!$product || $product->get_type() === 'variable') continue;
            $length = pfp_get_product_length($product);
            if ($length <= 1) continue;
            $base_price = $product->get_price();
            $total_price = floatval($base_price) * $length;
            $cart_item['data']->set_price($total_price);
        }
    }
}
