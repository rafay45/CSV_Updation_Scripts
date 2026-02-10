<?php
/**
 * Correct the CSV Categories to Main > Subcategory Format
 * This script properly maps products to MainCategory > Subcategory format
 */

$master_file = 'C:\\Users\\user\\Downloads\\Master File - Web Clean - Export_Clean.csv';
$wholesale_file = 'C:\\Users\\user\\Downloads\\WholesalefencingCSV - CSVWholesalefencing - MappingCSVfileAccordingToWordpress_WithCategories (1) (1).csv';
$output_file = 'C:\\Users\\user\\Downloads\\Wholesale_Products_With_Categories_CORRECTED.csv';

// Category name mappings
$category_names = array(
    'VINYL' => 'Vinyl',
    'CHAIN LINK' => 'Chain Link',
    'ORNAMENTAL' => 'Ornamental',
    'MODERN' => 'Modern',
    'SIMTEK' => 'Simtek',
    'TREX' => 'Trex',
    'WOOD' => 'Wood',
    'AGRICULTURE' => 'Agriculture',
    'METAL HORSE FENCE' => 'Metal Horse Fence'
);

// Subcategory style columns
$subcategory_columns = array(
    'SMOOTH TOP' => 'Smooth Top',
    'POINT TOP' => 'Point Top',
    'SQUARE TOP' => 'Square Top',
    'CURVED TOP' => 'Curved Top',
    'BARBWIRE' => 'Barbwire'
);

echo "========================================\n";
echo "CORRECTING CSV CATEGORIES\n";
echo "========================================\n\n";

// Read Master File with correct header row
echo "Reading Master File...\n";
$master_handle = fopen($master_file, 'r');
$master_header = array();
$master_data = array();
$row_num = 0;

while (($row = fgetcsv($master_handle)) !== FALSE) {
    $row_num++;
    if ($row_num == 5) { // Header at row 4
        $master_header = $row;
    } elseif ($row_num > 5) {
        $master_data[] = array_combine($master_header, $row);
    }
}
fclose($master_handle);

echo "Master File loaded: " . count($master_data) . " products\n\n";

// Create mapping
echo "Creating category mapping...\n";
$mapping = array();

foreach ($master_data as $product) {
    $sku = $product['Product Code'] ?? '';
    if (!$sku) continue;
    
    $category = '';
    $subcategory = '';
    
    // Check each main category column
    foreach ($category_names as $col_name => $display_name) {
        if (!empty($product[$col_name])) {
            $category = $display_name;
            break;
        }
    }
    
    // Check for subcategory
    if ($category) {
        foreach ($subcategory_columns as $col_name => $display_name) {
            if (!empty($product[$col_name])) {
                $subcategory = $display_name;
                break;
            }
        }
    }
    
    // Build full category string
    if ($subcategory) {
        $mapping[$sku] = "$category > $subcategory";
    } else {
        $mapping[$sku] = $category;
    }
}

echo "Mapping created for " . count($mapping) . " products\n\n";

// Read, update, and write Wholesale CSV
echo "Processing Wholesale CSV...\n";
$wholesale_handle = fopen($wholesale_file, 'r');
$output_handle = fopen($output_file, 'w');

$header = fgetcsv($wholesale_handle);
$categories_col = array_search('Categories', $header);

if ($categories_col === FALSE) {
    die("ERROR: Categories column not found!\n");
}

fputcsv($output_handle, $header);

$matched = 0;
$unmatched = 0;
$row_count = 0;

while (($row = fgetcsv($wholesale_handle)) !== FALSE) {
    $row_count++;
    $sku = $row[1] ?? ''; // SKU is typically in column 1
    
    if (isset($mapping[$sku])) {
        $row[$categories_col] = $mapping[$sku];
        $matched++;
    } else {
        $unmatched++;
    }
    
    fputcsv($output_handle, $row);
}

fclose($wholesale_handle);
fclose($output_handle);

echo "\n========================================\n";
echo "CORRECTION COMPLETED\n";
echo "========================================\n";
echo "Total products processed: " . $row_count . "\n";
echo "Matched with Master File: " . $matched . "\n";
echo "Unmatched (kept existing): " . $unmatched . "\n";
echo "\nOutput file saved to:\n";
echo $output_file . "\n";
echo "\n✅ Categories are now in proper 'Main > Subcategory' format!\n";
?>
