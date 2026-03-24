// ==========================================
// Per Foot Pricing - FINAL VERSION (No JS, No Layout Break)
// ==========================================

// Check if product has UOM = LNF
if (!function_exists('pfp_is_linear_feet_product')) {
    function pfp_is_linear_feet_product($product) {
        if (!$product) return false;
        $attributes = $product->get_attributes();

        foreach ($attributes as $attr_name => $attr_obj) {
            $label = strtolower(wc_attribute_label($attr_name));
            if ($label === 'uom' || $label === 'unit of measurement' || $label === 'unit of measure') {
                if (is_object($attr_obj) && method_exists($attr_obj, 'is_taxonomy')) {
                    if ($attr_obj->is_taxonomy()) {
                        $terms = wp_get_post_terms($product->get_id(), $attr_name, array('fields' => 'names'));
                        if (!is_wp_error($terms) && !empty($terms)) {
                            return strtoupper(trim($terms[0])) === 'LNF';
                        }
                    } else {
                        $options = $attr_obj->get_options();
                        if (!empty($options)) {
                            $val = is_array($options) ? $options[0] : $options;
                            return strtoupper(trim($val)) === 'LNF';
                        }
                    }
                }
            }
        }
        return false;
    }
}

if (!function_exists('pfp_get_product_length')) {
    function pfp_get_product_length($product) {
        if (!$product) return 1;

        // IMPORTANT: Only process if UOM = LNF
        if (!pfp_is_linear_feet_product($product)) return 1;

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

// Modify price display (Price × Length)
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

// Add per foot info AFTER the main price
if (!function_exists('pfp_add_per_foot_info_to_price')) {
    add_filter('woocommerce_get_price_html', 'pfp_add_per_foot_info_to_price', 20, 2);
    function pfp_add_per_foot_info_to_price($price_html, $product) {
        if (!is_product()) return $price_html;
        if (!$product || $product->get_type() === 'variable') return $price_html;

        $length = pfp_get_product_length($product);
        if ($length <= 1) return $price_html;

        $base_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        if (!$base_price) return $price_html;

        $per_foot_price = $sale_price ? $sale_price : $base_price;

        // Green box with per foot price
        $price_html .= '<div style="margin-top: 10px; font-size: 14px; color: #666; text-align: right;">';
        $price_html .= '<span style="font-weight: 600;">PRICE PER FOOT</span> ' . wc_price($per_foot_price) . ' / ft';
        $price_html .= '</div>';

        // Yellow notice (added here to avoid layout issues)
        $price_html .= '<div class="pfp-notice" style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px 15px; margin: 15px 0; font-size: 13px; color: #856404; line-height: 1.5;">';
        $price_html .= '<strong>Note:</strong> This product is sold as a complete piece of ' . $length . ' feet. Quantity of 1 = ' . $length . ' feet.';
        $price_html .= '</div>';

        return $price_html;
    }
}

// Update cart price
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