<?php
/**
 * fix_product_slugs.php
 *
 * Ye script CSV file se product slugs padhkar
 * WordPress mein SKU se product dhundhti hai
 * aur slug update karti hai.
 *
 * CSV FILE: ProductData_FIXED.csv (same folder mein honi chahiye)
 * CSV FORMAT: Column 2 = SKU, Column 15 = Slug
 *
 * HOW TO USE:
 *   1. Is file ko WordPress root mein upload karo (jahan wp-config.php hai)
 *   2. ProductData_FIXED.csv bhi wahi upload karo
 *   3. Browser mein open karo: https://yoursite.com/fix_product_slugs.php
 *   4. Done hone ke baad FILE DELETE KAR DO
 */

@ini_set('memory_limit', '512M');
@set_time_limit(300);

$wp_load_path = dirname(__FILE__) . '/wp-load.php';
if (!file_exists($wp_load_path)) {
    die('ERROR: wp-load.php nahi mila. Is file ko WordPress root folder mein rakho.');
}
require_once($wp_load_path);

if (!current_user_can('manage_options')) {
    wp_die('Access denied. Pehle WordPress admin mein login karo.');
}

// Debug: PHP errors show karo
@ini_set('display_errors', 1);
@error_reporting(E_ALL);

// CSV file path — same folder mein honi chahiye
$csv_file = dirname(__FILE__) . '/ProductData_FIXED.csv';
if (!file_exists($csv_file)) {
    die('ERROR: ProductData_FIXED.csv nahi mili. File WordPress root mein rakho.');
}

// Batch processing — URL mein ?offset=0, ?offset=500, etc.
$batch_size = 500;
$offset     = isset($_GET['offset']) ? max(0, intval($_GET['offset'])) : 0;

$results  = [];
$updated  = 0;
$skipped  = 0;
$notfound = 0;
$total_rows = 0;

if (($handle = fopen($csv_file, 'r')) !== false) {
    $row_num = 0;
    $data_row = 0; // header ke baad
    while (($row = fgetcsv($handle, 5000, ',')) !== false) {
        $row_num++;
        if ($row_num === 1) continue; // header skip

        $data_row++;
        $total_rows++;

        // offset se pehle skip karo
        if ($data_row <= $offset) continue;
        // batch size ke baad stop karo
        if ($data_row > $offset + $batch_size) { $total_rows--; continue; }

        $sku  = isset($row[1]) ? trim($row[1]) : '';
        $slug = isset($row[14]) ? trim($row[14]) : '';

        if (empty($sku) || empty($slug)) {
            $skipped++;
            continue;
        }

        $product_id = wc_get_product_id_by_sku($sku);

        if (!$product_id) {
            $results[] = ['status' => 'notfound', 'sku' => $sku, 'slug' => $slug, 'msg' => 'SKU nahi mila'];
            $notfound++;
            continue;
        }

        $post = get_post($product_id);
        if ($post->post_name === sanitize_title($slug)) {
            $skipped++;
            continue;
        }

        $update = wp_update_post([
            'ID'        => $product_id,
            'post_name' => sanitize_title($slug),
        ]);

        if (is_wp_error($update)) {
            $results[] = ['status' => 'error', 'sku' => $sku, 'slug' => $slug, 'msg' => $update->get_error_message()];
        } else {
            $results[] = ['status' => 'ok', 'sku' => $sku, 'slug' => $slug, 'msg' => 'Updated (ID: ' . $product_id . ')'];
            $updated++;
        }
    }
    fclose($handle);
}

$next_offset = $offset + $batch_size;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Product Slug Fix</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 40px auto; padding: 20px; }
        h1 { color: #244e5a; }
        .summary { background: #f0f8f0; border: 1px solid #32703B; border-radius: 8px; padding: 16px; margin: 20px 0; font-size: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        th { background: #244e5a; color: white; padding: 8px 10px; text-align: left; }
        td { padding: 6px 10px; border-bottom: 1px solid #eee; }
        .ok { color: #32703B; font-weight: bold; }
        .notfound { color: #cc6600; font-weight: bold; }
        .skip { color: #aaa; }
        .error { color: #cc0000; font-weight: bold; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 14px; margin-top: 24px; }
    </style>
</head>
<body>
    <h1>Product Slug Fix — Results</h1>

    <div class="summary">
        Batch: <strong><?php echo $offset + 1; ?> – <?php echo $offset + $batch_size; ?></strong> &nbsp;|&nbsp;
        ✅ Updated: <strong style="color:#32703B"><?php echo $updated; ?></strong> &nbsp;|&nbsp;
        ⚠️ Not found: <strong style="color:#cc6600"><?php echo $notfound; ?></strong> &nbsp;|&nbsp;
        ⏭️ Skipped: <strong style="color:#888"><?php echo $skipped; ?></strong>
    </div>

    <?php if (count($results) > 0 || $updated > 0 || $notfound > 0): ?>
    <div style="margin: 16px 0;">
        <a href="?offset=<?php echo $next_offset; ?>" style="background:#244e5a;color:#fff;padding:10px 24px;border-radius:6px;text-decoration:none;font-weight:bold;">
            ▶ Next Batch (<?php echo $next_offset + 1; ?>–<?php echo $next_offset + $batch_size; ?>)
        </a>
    </div>
    <?php endif; ?>

    <table>
        <tr>
            <th>Status</th>
            <th>SKU</th>
            <th>New Slug</th>
            <th>Message</th>
        </tr>
        <?php foreach ($results as $r): ?>
        <tr>
            <td class="<?php echo esc_attr($r['status']); ?>">
                <?php
                $icons = ['ok' => '✅', 'notfound' => '⚠️', 'skip' => '⏭️', 'error' => '❌'];
                echo ($icons[$r['status']] ?? '?') . ' ' . strtoupper($r['status']);
                ?>
            </td>
            <td><?php echo esc_html($r['sku']); ?></td>
            <td><?php echo esc_html($r['slug']); ?></td>
            <td><?php echo esc_html($r['msg']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div class="warning">
        <strong>⚠️ IMPORTANT:</strong> Ab ye file aur CSV file dono server se <strong>DELETE</strong> karo.
    </div>
</body>
</html>
