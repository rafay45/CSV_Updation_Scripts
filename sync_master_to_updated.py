import csv
import sys

def sync_files(master_file, target_file, output_file):
    """
    Sync changes from Master file to the updated short descriptions file.
    Keeps the short descriptions from target file but updates everything else from master.
    """
    print("="*70)
    print("Syncing Master File changes to Updated Short Descriptions")
    print("="*70)
    print(f"\nReading Master file: {master_file}")

    # Read master file - skip first 5 header rows
    master_data = {}
    with open(master_file, 'r', encoding='utf-8') as f:
        # Skip first 5 rows (headers)
        for _ in range(5):
            next(f)

        # Now read with DictReader
        reader = csv.DictReader(f)

        for row in reader:
            product_code = row.get('Product Code', '').strip()
            if product_code and product_code not in ['Product Code', '', 'None']:
                master_data[product_code] = row

    print(f"Found {len(master_data)} products in Master file")

    # Read target file with updated short descriptions
    print(f"\nReading target file: {target_file}")
    target_data = []
    target_fieldnames = None

    with open(target_file, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        target_fieldnames = reader.fieldnames

        for row in reader:
            target_data.append(row)

    print(f"Found {len(target_data)} products in target file")

    # Sync changes
    print(f"\nSyncing changes...")
    updated_count = 0
    not_found_count = 0

    for idx, target_row in enumerate(target_data, 1):
        sku = target_row.get('SKU', '').strip()

        if not sku:
            continue

        # Find matching product in master file
        if sku in master_data:
            master_row = master_data[sku]

            # Update all fields EXCEPT Short description
            # Keep the generated short description, update everything else
            saved_short_desc = target_row.get('Short description', '')

            # Map master file fields to WooCommerce import fields
            # Update Name from H1 Title Text
            if master_row.get('H1 Title Text'):
                target_row['Name'] = master_row['H1 Title Text']

            # Update Description from Description Text For Website
            if master_row.get('Description Text For Website'):
                target_row['Description'] = master_row['Description Text For Website']

            # Update Meta description
            if master_row.get('Meta Data'):
                target_row['Meta: _yoast_wpseo_metadesc'] = master_row['Meta Data']

            # Update slug
            if master_row.get('Url Slug'):
                target_row['Slug'] = master_row['Url Slug']

            # Update images
            if master_row.get('Product Part Image'):
                images = [master_row.get('Product Part Image', '')]
                # Add additional images if present
                for i in range(2, 6):
                    img_key = f'Image {i}'
                    if master_row.get(img_key):
                        images.append(master_row[img_key])
                target_row['Images'] = ', '.join([img for img in images if img])

            # Update price from RETAIL1
            if master_row.get('RETAIL1'):
                target_row['Regular price'] = master_row['RETAIL1'].replace('$', '').strip()

            # Update stock status
            if master_row.get('IN STOCK'):
                target_row['In stock?'] = '1' if master_row['IN STOCK'].lower() == 'in stock' else '0'

            # Update weight
            if master_row.get('UNIT WEIGHT'):
                target_row['Weight'] = master_row['UNIT WEIGHT']

            # Restore the short description (keep our generated one)
            target_row['Short description'] = saved_short_desc

            updated_count += 1

            if idx % 100 == 0:
                print(f"  Progress: {idx}/{len(target_data)} products processed...")
        else:
            not_found_count += 1

    # Write synced file
    print(f"\n{'='*70}")
    print(f"Writing synced CSV to: {output_file}")

    with open(output_file, 'w', encoding='utf-8', newline='') as f:
        writer = csv.DictWriter(f, fieldnames=target_fieldnames)
        writer.writeheader()
        writer.writerows(target_data)

    print(f"\n{'='*70}")
    print("SUMMARY")
    print(f"{'='*70}")
    print(f"Total products in target file: {len(target_data)}")
    print(f"Updated from master file: {updated_count}")
    print(f"Not found in master file: {not_found_count}")
    print(f"\nOutput saved to: {output_file}")
    print(f"{'='*70}")

if __name__ == "__main__":
    master_file = r"C:\Users\user\Downloads\Master File - Web Clean - 18-03-26 - Export_Clean New.csv"
    target_file = r"C:\Users\user\Downloads\FencingsProductData_Updated_ShortDescriptions_v2.csv"
    output_file = r"C:\Users\user\Downloads\FencingsProductData_Final_Synced.csv"

    print("Starting sync process...")
    print()

    try:
        sync_files(master_file, target_file, output_file)
        print("\nSync Complete!")
    except Exception as e:
        print(f"\nERROR: {str(e)}")
        import traceback
        traceback.print_exc()

    print("="*70)
