import pandas as pd
import os

# File paths
master_file = r'C:\Users\user\Downloads\Master File - Web Clean - Export_Clean.csv'
wholesale_file = r'C:\Users\user\Downloads\WholesalefencingCSV - CSVWholesalefencing - MappingCSVfileAccordingToWordpress_WithCategories (1) (1).csv'
output_file = r'C:\Users\user\Downloads\Wholesale_Products_With_Categories_CORRECTED.csv'

# Category name mappings
category_names = {
    'VINYL': 'Vinyl',
    'CHAIN LINK': 'Chain Link',
    'ORNAMENTAL': 'Ornamental',
    'MODERN': 'Modern',
    'SIMTEK': 'Simtek',
    'TREX': 'Trex',
    'WOOD': 'Wood',
    'AGRICULTURE': 'Agriculture',
    'METAL HORSE FENCE': 'Metal Horse Fence'
}

# Subcategory style columns
subcategory_columns = {
    'SMOOTH TOP': 'Smooth Top',
    'POINT TOP': 'Point Top',
    'SQUARE TOP': 'Square Top',
    'CURVED TOP': 'Curved Top',
    'BARBWIRE': 'Barbwire'
}

print("="*70)
print("CORRECTING CSV CATEGORIES TO MAIN > SUBCATEGORY FORMAT")
print("="*70)
print()

# Read Master File with correct header row (row 4 = index 3)
print("Reading Master File...")
master_df = pd.read_csv(master_file, header=4)
print(f"Master File loaded: {len(master_df)} products")
print(f"Columns found: {list(master_df.columns[:15])}...")
print()

# Create category mapping
print("Creating category mapping from Master File...")
mapping = {}

for idx, row in master_df.iterrows():
    sku = row.get('Product Code', '')
    if pd.isna(sku) or not str(sku).strip():
        continue
    
    sku = str(sku).strip()
    category = ''
    subcategory = ''
    
    # Check each main category column
    for col_name, display_name in category_names.items():
        if col_name in row.index:
            val = row[col_name]
            if pd.notna(val) and str(val).strip():
                category = display_name
                break
    
    # Check for subcategory
    if category:
        for col_name, display_name in subcategory_columns.items():
            if col_name in row.index:
                val = row[col_name]
                if pd.notna(val) and str(val).strip():
                    subcategory = display_name
                    break
    
    # Build full category string
    if category:
        if subcategory:
            mapping[sku] = f"{category} > {subcategory}"
        else:
            mapping[sku] = category

print(f"Mapping created for {len(mapping)} products")
print()

# Sample mappings
print("Sample mappings:")
for i, (sku, cat) in enumerate(list(mapping.items())[:5]):
    print(f"  SKU {sku}: {cat}")
print()

# Read Wholesale CSV
print("Reading Wholesale CSV...")
wholesale_df = pd.read_csv(wholesale_file)
print(f"Wholesale CSV loaded: {len(wholesale_df)} products, {len(wholesale_df.columns)} columns")
print()

# Update categories
print("Updating categories in Wholesale CSV...")
matched = 0
unmatched = 0

for idx, row in wholesale_df.iterrows():
    sku = str(row.get('SKU', '')).strip()
    
    if sku in mapping:
        wholesale_df.at[idx, 'Categories'] = mapping[sku]
        matched += 1
    else:
        unmatched += 1

print(f"Matched: {matched} products")
print(f"Unmatched (kept existing): {unmatched} products")
print()

# Save output file
print(f"Saving corrected CSV to:")
print(f"{output_file}")
wholesale_df.to_csv(output_file, index=False)

print()
print("="*70)
print("✅ CORRECTION COMPLETED SUCCESSFULLY!")
print("="*70)
print()
print(f"Output file: Wholesale_Products_With_Categories_CORRECTED.csv")
print(f"Total products: {len(wholesale_df)}")
print()
print("Sample corrected categories:")
for idx in range(min(10, len(wholesale_df))):
    sku = wholesale_df.iloc[idx]['SKU']
    cat = wholesale_df.iloc[idx]['Categories']
    print(f"  SKU {sku}: {cat}")
