import pandas as pd
master = pd.read_csv('Master File - Web Clean - Export_Clean.csv', header=4, encoding='utf-8', low_memory=False)

print("First few products with their categories:")
cols_to_show = ['Product Code', 'DESCRIPTION', 'VINYL', 'CHAIN LINK', 'ORNAMENTAL', 'MODERN', 'SIMTEK', 'TREX', 'AGRICULTURE', 'METAL HORSE FENCE']

print("\nColumns around index 46-55 (where Smooth/Point Top are):") 
for i in range(45, 55):
    print(f"  {i}: {master.columns[i]}")

print("\nFirst 2 rows of category data:")
print(master[cols_to_show].head(2).to_string())

print("\n\nRow 0 (SKU", master['Product Code'].iloc[0], "):")
sku0 = master['Product Code'].iloc[0]
print(f"VINYL: {master['VINYL'].iloc[0]}")
print(f"CHAIN LINK: {master['CHAIN LINK'].iloc[0]}")
print(f"ORNAMENTAL: {master['ORNAMENTAL'].iloc[0]}")
print(f"MODERN: {master['MODERN'].iloc[0]}")
print(f"SIMTEK: {master['SIMTEK'].iloc[0]}")
print(f"TREX: {master['TREX'].iloc[0]}")
print(f"AGRICULTURE: {master['AGRICULTURE'].iloc[0]}")
print(f"METAL HORSE FENCE: {master['METAL HORSE FENCE'].iloc[0]}")

# Check what columns have values in the "filter" range
print("\n\nColumns 45-55 for first product (Ornamental or similar):")
for i in range(45, 56):
    val = master[master.columns[i]].iloc[0]
    if pd.notna(val) and val != '':
        print(f"  {master.columns[i]}: {val}")
