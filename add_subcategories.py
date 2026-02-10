import pandas as pd
import sys

# Windows encoding fix
if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8')

print("Reading files...")

# Read master file (with skiprows=4 because headers on row 5)
master = pd.read_csv(r'C:\Users\user\Downloads\Master File - Web Clean - Export_Clean.csv',
                      encoding='utf-8', skiprows=4, low_memory=False)

# Read target file
target = pd.read_csv(r'C:\Users\user\Downloads\Wholesale_Products_With_Categories_Updated.csv',
                      encoding='utf-8')

print(f"Master file: {len(master)} rows")
print(f"Target file: {len(target)} rows")

def determine_subcategory(row):
    """Determine sub-category based on fence style columns from master file"""
    subcategories = []

    # Check fence style columns (column names from master file)
    styles = {
        'PRIVACY': 'Privacy',
        'LATTICE': 'Lattice',
        '2 RAIL': '2-Rail',
        '3 RAIL': '3-Rail',
        '4 RAIL': '4-Rail',
        'PICKET': 'Picket',
        'SCALLOPED PICKET': 'Scalloped Picket',
        'POOL': 'Pool',
        'SMOOTH TOP': 'Smooth Top',
        'POINT TOP': 'Point Top',
        'SQUARE TOP': 'Square Top',
        'CURVED TOP': 'Curved Top',
        'HORIZONTAL': 'Horizontal',
        'BARBWIRE': 'Barbwire',
        'FIELD FENCE': 'Field Fence'
    }

    for col, name in styles.items():
        if col in row and pd.notna(row[col]) and str(row[col]).strip():
            # If column has any value, add this style
            subcategories.append(name)

    return subcategories

# Create SKU to subcategory mapping from master file
print("Creating subcategory mapping...")
sku_to_subcat = {}

for idx, row in master.iterrows():
    if pd.notna(row.get('Product Code')):
        sku = str(row['Product Code']).strip()
        subcats = determine_subcategory(row)
        if subcats:
            sku_to_subcat[sku] = subcats

print(f"Found subcategories for {len(sku_to_subcat)} SKUs")

# Update target file categories
print("Updating target file...")
updated_count = 0

for idx, row in target.iterrows():
    sku = str(row['SKU']).strip()

    if sku in sku_to_subcat:
        subcats = sku_to_subcat[sku]
        current_cat = str(row['Categories']).strip()

        # Build category hierarchy: Main Category > Sub-Category
        if current_cat and current_cat != 'nan' and current_cat != 'Uncategorized':
            # Add sub-categories to main category
            category_str = current_cat
            for subcat in subcats:
                category_str += f" > {subcat}"

            target.at[idx, 'Categories'] = category_str
            updated_count += 1

print(f"Updated {updated_count} products with sub-categories")

# Save updated file
output_file = r'C:\Users\user\Downloads\Wholesale_Products_With_Subcategories.csv'
target.to_csv(output_file, index=False, encoding='utf-8-sig')

print(f"\n[SUCCESS] File saved: {output_file}")
print(f"Total products: {len(target)}")

# Show sample categories
print("\nSample category structures:")
sample_cats = target[target['Categories'].str.contains('>', na=False)]['Categories'].head(10)
for cat in sample_cats:
    print(f"  - {cat}")
