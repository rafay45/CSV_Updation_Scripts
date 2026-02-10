import pandas as pd

output = pd.read_csv('Wholesale_Products_With_Categories_Updated.csv', low_memory=False)

print('Output file verification:')
print(f'Total rows: {len(output)}')
print(f'Total columns: {len(output.columns)}')
print(f'Categories column found: {"Categories" in output.columns}')

print('\nFirst 15 rows (SKU and Categories):')
for idx, row in output.head(15).iterrows():
    sku = row['SKU']
    cat = row['Categories']
    print(f'  SKU {sku}: {cat}')

print('\n\nCategory summary by count:')
for cat, count in output['Categories'].value_counts().items():
    print(f'  {cat}: {count} products')

# Check for the new categories requested
new_cats = ['Vinyl', 'Modern', 'Simtek', 'Trex', 'Metal Horse Fence']
print('\n\nNew categories summary:')
for nc in new_cats:
    count = len(output[output['Categories'].str.contains(nc, na=False, case=False)])
    print(f'  {nc}: {count} products')
