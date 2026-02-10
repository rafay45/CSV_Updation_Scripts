import pandas as pd
master = pd.read_csv('Master File - Web Clean - Export_Clean.csv', header=4, encoding='utf-8', low_memory=False)

# Look at different category products and their style/filter indicators
print("ORNAMENTAL products and their styles:")
orn_prods = master[master['ORNAMENTAL'].notna()].head(5)
for idx, row in orn_prods.iterrows():
    print(f"\nSKU {row['Product Code']}:")
    # Check style columns
    for col in ['SMOOTH TOP', 'POINT TOP', 'SQUARE TOP', 'CURVED TOP', 'HORIZONTAL ALUMINUM']:
        val = row[col]
        if pd.notna(val) and val != '':
            print(f"  {col}: {val}")

print("\n\nVINYL products and their styles/filters (looking for subcategory info):")
# Find vinyl products that aren't multi-category
vinyl_prod = master[(master['VINYL'].notna()) & (master['CHAIN LINK'].isna())].head(3)
print(f"Found {len(vinyl_prod)} solo-vinyl products\n")

for idx, row in vinyl_prod.iterrows():
    print(f"SKU {row['Product Code']}: {row['DESCRIPTION']}")
    # Check columns
    for col in master.columns:
        val = row[col]
        if pd.notna(val) and val != '' and col not in ['Data Status', 'MANUFACTURE', 'Vendor', 'Product Code', 'Brand', 'Product Line', 'PRODUCT CATEGORY']:
            if isinstance(val, str) and len(val) < 40:
                if any(x in col for x in ['Filter', 'Subcategory', 'Size', 'Post', 'Picket', 'Vinyl', 'Filter']):
                    print(f"  {col}: {val}")
