import pandas as pd
master = pd.read_csv('Master File - Web Clean - Export_Clean.csv', header=4, encoding='utf-8', low_memory=False)

# Find different category products
categories = ['VINYL', 'MODERN', 'SIMTEK', 'TREX', 'METAL HORSE FENCE']

for cat in categories:
    idx = master[master[cat].notna()].index
    if len(idx) > 0:
        idx = idx[0]
        print(f"\n{cat} - SKU {master['Product Code'].iloc[idx]}:")
        print(f"  Description: {master['DESCRIPTION'].iloc[idx]}")
        
        # Show category value
        for c in categories:
            val = master[c].iloc[idx]
            if pd.notna(val) and val != '':
                print(f"  {c}: {val}")
        
        # Show style columns
        for col in ['Vinyl Subcategory', 'Vinyl Post Filter', 'Vinyl Filter', 'Modern Subcategory', 'Simtek Subcategory', 'Trex Subcategory']:
            if col in master.columns:
                val = master[col].iloc[idx]
                if pd.notna(val) and val != '':
                    print(f"  {col}: {val}")

# Check if there are columns containing "Vinyl" or "Modern" as part of their name
print("\n\nAll columns containing 'Vinyl', 'Modern', 'Simtek', or 'Trex':")
for col in master.columns:
    if any(x in col for x in ['Vinyl', 'Modern', 'Simtek', 'Trex']):
        print(f"  {col}")
