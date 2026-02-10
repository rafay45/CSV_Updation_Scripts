import pandas as pd
master = pd.read_csv('Master File - Web Clean - Export_Clean.csv', header=4, encoding='utf-8', low_memory=False)

# Find an Ornamental product
orn_idx = master[master['ORNAMENTAL'].notna()].index[0]

print(f"First Ornamental product at index {orn_idx}:")
print(f"SKU: {master['Product Code'].iloc[orn_idx]}")
print(f"Description: {master['DESCRIPTION'].iloc[orn_idx]}")

# Show the category columns
cols_to_show = ['VINYL', 'CHAIN LINK', 'ORNAMENTAL', 'MODERN', 'SIMTEK', 'TREX', 'AGRICULTURE', 'METAL HORSE FENCE']
print("\nCategory columns:")
for col in cols_to_show:
    val = master[col].iloc[orn_idx]
    print(f"  {col}: {val}")

# Show "style" columns - Smooth Top, Point Top, etc.
style_cols = ['SMOOTH TOP', 'POINT TOP', 'SQUARE TOP', 'CURVED TOP']
print("\nStyle columns:")
for col in style_cols:
    val = master[col].iloc[orn_idx]
    if pd.notna(val) and val != '':
        print(f"  {col}: {val}")

# Try to find pattern by looking at what's in row
print(f"\nAll non-nan values in row {orn_idx}:")
for col in master.columns:
    val = master[col].iloc[orn_idx]
    if pd.notna(val) and val != '' and val != 'New':
        if isinstance(val, str) and len(val) < 50:
            print(f"  {col}: {val}")
