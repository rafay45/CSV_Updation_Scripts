import pandas as pd
master = pd.read_csv('Master File - Web Clean - Export_Clean.csv', header=4, encoding='utf-8', low_memory=False)
wholesale = pd.read_csv('WholesalefencingCSV - CSVWholesalefencing - MappingCSVfileAccordingToWordpress_WithCategories (1) (1).csv', encoding='utf-8', low_memory=False)

# Merge to see what currently is in the Wholesale CSV
master['Product Code'] = master['Product Code'].astype(str).str.strip()
wholesale['SKU'] = wholesale['SKU'].astype(str).str.strip()

merged = wholesale.merge(master, left_on='SKU', right_on='Product Code', how='left')

print("Sample of merged data (first 5 rows with visible columns):")
cols = ['SKU', 'Name', 'Categories', 'VINYL', 'CHAIN LINK', 'ORNAMENTAL', 'MODERN', 'SIMTEK', 'TREX', 'AGRICULTURE', 'METAL HORSE FENCE']
for col in cols:
    if col not in merged.columns:
        cols.remove(col)

sample = merged[cols].head(10)
for col in sample.columns:
    print(f"{col}:")
    for i, val in enumerate(sample[col]):
        if pd.notna(val) and val != '':
            print(f"  Row {i}: {val}")
    print()
