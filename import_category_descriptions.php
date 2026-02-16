<?php
/**
 * import_category_descriptions.php
 *
 * Ye script WooCommerce product categories ki descriptions
 * ek CSV file se import karti hai.
 *
 * CSV FORMAT (2 columns):
 *   Column 1: category_name  (jaise: "Chain Link Fabric", "Fittings", "Top Rail")
 *   Column 2: description    (category ki description text)
 *
 * HOW TO USE:
 *   1. CSV file banao — pehli row headers honi chahiye: category_name,description
 *   2. Is file ko WordPress root mein upload karo (jahan wp-config.php hai)
 *   3. Browser mein open karo: https://yoursite.com/import_category_descriptions.php
 *   4. Script run hone ke baad FILE DELETE KAR DO server se
 *
 * SECURITY NOTE: File run hone ke baad FORAN delete karo
 */

// ── WordPress load karo ──────────────────────────────────────────────────────
define('ABSPATH_CHECK', true);
$wp_load_path = dirname(__FILE__) . '/wp-load.php';
if (!file_exists($wp_load_path)) {
    die('ERROR: wp-load.php nahi mila. Is file ko WordPress root folder mein rakho (jahan wp-config.php hai).');
}
require_once($wp_load_path);

// ── Only admin run kar sake ──────────────────────────────────────────────────
if (!current_user_is_admin_or_skip()) {
    wp_die('Access denied. Pehle WordPress admin mein login karo.');
}

function current_user_is_admin_or_skip() {
    // CLI se run ho raha hai ya logged in admin hai
    if (php_sapi_name() === 'cli') return true;
    return current_user_can('manage_options');
}

// ── CSV file path ────────────────────────────────────────────────────────────
// CSV file is PHP file ke saath same folder mein honi chahiye
$csv_file = dirname(__FILE__) . '/category_descriptions.csv';

if (!file_exists($csv_file)) {
    die('ERROR: category_descriptions.csv nahi mili. File is PHP script ke saath same folder mein rakho.');
}

// ── Process karo ────────────────────────────────────────────────────────────
$results  = [];
$updated  = 0;
$skipped  = 0;
$notfound = 0;

if (($handle = fopen($csv_file, 'r')) !== false) {
    $row_num = 0;
    while (($row = fgetcsv($handle, 2000, ',')) !== false) {
        $row_num++;

        // Pehli row headers skip karo
        if ($row_num === 1) continue;

        // Empty rows skip karo
        if (empty($row[0])) continue;

        $cat_name    = trim($row[0]);
        $description = isset($row[1]) ? trim($row[1]) : '';

        if (empty($description)) {
            $results[] = ['status' => 'skip', 'name' => $cat_name, 'msg' => 'Description empty thi'];
            $skipped++;
            continue;
        }

        // Category dhundhein by exact name
        $term = get_term_by('name', $cat_name, 'product_cat');

        // Agar name se nahi mila to slug se try karo
        if (!$term) {
            $slug = sanitize_title($cat_name);
            $term = get_term_by('slug', $slug, 'product_cat');
        }

        if (!$term || is_wp_error($term)) {
            $results[] = ['status' => 'notfound', 'name' => $cat_name, 'msg' => 'Category WordPress mein nahi mili'];
            $notfound++;
            continue;
        }

        // Description update karo
        $update = wp_update_term($term->term_id, 'product_cat', [
            'description' => wp_kses_post($description),
        ]);

        if (is_wp_error($update)) {
            $results[] = ['status' => 'error', 'name' => $cat_name, 'msg' => $update->get_error_message()];
        } else {
            $results[] = ['status' => 'ok', 'name' => $cat_name, 'msg' => 'Updated (ID: ' . $term->term_id . ')'];
            $updated++;
        }
    }
    fclose($handle);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Category Description Import</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 20px; }
        h1 { color: #244e5a; }
        .summary { background: #f0f8f0; border: 1px solid #32703B; border-radius: 8px; padding: 16px; margin: 20px 0; }
        .summary span { font-weight: bold; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #244e5a; color: white; padding: 10px; text-align: left; }
        td { padding: 8px 10px; border-bottom: 1px solid #eee; font-size: 13px; }
        .ok { color: #32703B; font-weight: bold; }
        .notfound { color: #cc6600; font-weight: bold; }
        .skip { color: #888; }
        .error { color: #cc0000; font-weight: bold; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 14px; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Category Description Import — Results</h1>

    <div class="summary">
        Total processed: <span><?php echo count($results); ?></span> &nbsp;|&nbsp;
        ✅ Updated: <span style="color:#32703B"><?php echo $updated; ?></span> &nbsp;|&nbsp;
        ⚠️ Not found: <span style="color:#cc6600"><?php echo $notfound; ?></span> &nbsp;|&nbsp;
        ⏭️ Skipped: <span style="color:#888"><?php echo $skipped; ?></span>
    </div>

    <table>
        <tr>
            <th>Status</th>
            <th>Category Name</th>
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
            <td><?php echo esc_html($r['name']); ?></td>
            <td><?php echo esc_html($r['msg']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div class="warning">
        <strong>⚠️ IMPORTANT:</strong> Ab ye file aur <code>category_descriptions.csv</code> dono server se DELETE karo.
        Security ke liye ye zaroori hai.
    </div>
</body>
</html>
