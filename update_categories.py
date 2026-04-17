import csv
from collections import defaultdict

def build_category_from_master(master_row):
    """
    Build WooCommerce category hierarchy from master file row.

    Mapping (from Master file line 4 headers):
      Main Category  = VINYL, CHAIN LINK, ORNAMENTAL, MODERN, SIMTEK, TREX,
                       AGRICULTURE, METAL HORSE FENCE, WOOD, PRIVACY
      Sub Category   = subcategory columns per main category:
                         Vinyl Subcategory     → LATTICE, 2 RAIL, 3 RAIL, 4 RAIL, PICKET, SCALLOPED PICKET, POOL
                         Ornamental Subcategory→ SMOOTH TOP, POINT TOP, SQUARE TOP, CURVED TOP
                         Modern Subcategory    → HORIZONTAL ALUMINUM, ALUMINUM WITH FILL, VERTICAL ALUMINUM
                         Simtek Subcategory    → ECO STONE, ECO WOOD
                         Trex Subcategory      → SECLUSIONS, HORIZONS
                         Agriculture Subcategory → BARBWIRE, FIELD FENCE
                         (Chain Link, Metal Horse Fence, Wood, Privacy have no sub-categories)
      Product Category = PRODUCT CATEGORY column (Gate Hardware, Pipe, Post Caps...)
      Component Type   = Component Type column  (Hinges, Latch, Bands...)

    Final hierarchy:
      Main Category > Sub Category > Product Category > Component Type
    """

    # Define which sub-category columns belong to which main category
    main_to_subcats = {
        'VINYL':             ['PRIVACY', 'LATTICE', '2 RAIL', '3 RAIL', '4 RAIL', 'PICKET', 'SCALLOPED PICKET', 'POOL'],
        'CHAIN LINK':        [],
        'ORNAMENTAL':        ['SMOOTH TOP', 'POINT TOP', 'SQUARE TOP', 'CURVED TOP'],
        'MODERN':            ['HORIZONTAL ALUMINUM', 'ALUMINUM WITH FILL', 'VERTICAL ALUMINUM'],
        'SIMTEK':            ['ECO STONE', 'ECO WOOD'],
        'TREX':              ['SECLUSIONS', 'HORIZONS'],
        'AGRICULTURE':       ['BARBWIRE', 'FIELD FENCE'],
        'METAL HORSE FENCE': [],
        'WOOD':              [],
    }

    product_category = master_row.get('PRODUCT CATEGORY', '').strip()  # Product Category level
    component_type   = master_row.get('Component Type', '').strip()     # Component Type level

    category_strings = []

    for main_col, sub_cols in main_to_subcats.items():
        main_val = master_row.get(main_col, '').strip()
        if not main_val:
            continue  # Product not in this main category

        if sub_cols:
            filled_subs = []
            for sub_col in sub_cols:
                sub_val = master_row.get(sub_col, '').strip()
                if sub_val:
                    filled_subs.append(sub_val)

            if filled_subs:
                # One path per sub-category
                for sub_val in filled_subs:
                    # Fix known typos
                    sub_val = sub_val.replace('Seclusioins', 'Seclusions')
                    parts = [main_val, sub_val]
                    if product_category:
                        parts.append(product_category)
                    if component_type:
                        parts.append(component_type)
                    category_strings.append(' > '.join(parts))
            else:
                # No subcategory filled → SKIP this main category entirely
                continue
        else:
            # Main category with no sub-categories
            parts = [main_val]
            if product_category:
                parts.append(product_category)
            if component_type:
                parts.append(component_type)
            category_strings.append(' > '.join(parts))

    if not category_strings:
        return None

    # Deduplicate while preserving order
    seen = set()
    unique_strings = []
    for s in category_strings:
        if s not in seen:
            seen.add(s)
            unique_strings.append(s)

    return ', '.join(unique_strings)


def update_categories(master_file, target_file, output_file):
    print("=" * 70)
    print("Updating Categories from Master File")
    print("=" * 70)

    # Read master file
    print(f"\nReading master file...")
    master_data = {}
    with open(master_file, 'r', encoding='utf-8') as f:
        for _ in range(5):
            next(f)
        reader = csv.DictReader(f)
        for row in reader:
            pc = row.get('Product Code', '').strip()
            if pc:
                master_data[pc] = row

    print(f"Found {len(master_data)} products in master file")

    # Read target file
    print(f"Reading target file...")
    target_data = []
    target_fieldnames = None
    with open(target_file, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        target_fieldnames = reader.fieldnames
        for row in reader:
            target_data.append(row)

    print(f"Found {len(target_data)} products in target file")

    # Update categories
    print(f"\nUpdating categories...")
    updated_count = 0
    not_found_count = 0
    no_category_count = 0

    # Track what changed
    changes_log = []

    for target_row in target_data:
        sku = target_row.get('SKU', '').strip()
        if not sku:
            continue

        if sku in master_data:
            master_row = master_data[sku]
            new_category = build_category_from_master(master_row)

            if new_category:
                old_category = target_row.get('Categories', '').strip()

                if old_category != new_category:
                    changes_log.append({
                        'SKU': sku,
                        'Name': target_row.get('Name', ''),
                        'Old Category': old_category,
                        'New Category': new_category
                    })

                target_row['Categories'] = new_category
                updated_count += 1
            else:
                no_category_count += 1
        else:
            not_found_count += 1

    # Write output file
    print(f"\nWriting output to: {output_file}")
    with open(output_file, 'w', encoding='utf-8', newline='') as f:
        writer = csv.DictWriter(f, fieldnames=target_fieldnames)
        writer.writeheader()
        writer.writerows(target_data)

    # Write changes log
    log_file = output_file.replace('.csv', '_category_changes.csv')
    with open(log_file, 'w', encoding='utf-8', newline='') as f:
        writer = csv.DictWriter(f, fieldnames=['SKU', 'Name', 'Old Category', 'New Category'])
        writer.writeheader()
        writer.writerows(changes_log)

    print(f"\n{'=' * 70}")
    print("SUMMARY")
    print(f"{'=' * 70}")
    print(f"Total products in target: {len(target_data)}")
    print(f"Categories updated:       {updated_count}")
    print(f"Category changed (diff):  {len(changes_log)}")
    print(f"No category in master:    {no_category_count}")
    print(f"Not found in master:      {not_found_count}")
    print(f"\nOutput file:    {output_file}")
    print(f"Changes log:    {log_file}")
    print(f"{'=' * 70}")


if __name__ == "__main__":
    master_file = r"C:\Users\user\Downloads\Master File - Web Clean - 18-03-26 - Export_Clean New.csv"
    target_file = r"C:\Users\user\Downloads\FencingsProductData_Final_Synced.csv"
    output_file = r"C:\Users\user\Downloads\FencingsProductData_Categories_Updated.csv"

    update_categories(master_file, target_file, output_file)
