import pandas as pd

master = pd.read_csv('Master File - Web Clean - Export_Clean.csv', header=4, encoding='utf-8', low_memory=False)
wholesale = pd.read_csv('WholesalefencingCSV - CSVWholesalefencing - MappingCSVfileAccordingToWordpress_WithCategories (1) (1).csv', encoding='utf-8', low_memory=False)

master['Product Code'] = master['Product Code'].astype(str).str.strip()
wholesale['SKU'] = wholesale['SKU'].astype(str).str.strip()

# Find Ornamental products in both
orn_in_master = master[master['ORNAMENTAL'].notna()]['Product Code'].unique()[:10]

print(f"Checking {len(orn_in_master)} Ornamental products from Master File in Wholesale CSV:\n")
for sku in orn_in_master:
    ws_row = wholesale[wholesale['SKU'] == sku]
    if not ws_row.empty:
        cat = ws_row['Categories'].iloc[0]
        name = ws_row['Name'].iloc[0][:60]
        print(f"SKU {sku}:")
        print(f"  Name: {name}...")
        print(f"  Wholesale Category: {cat}")
        
        # Check Master data
        m_row = master[master['Product Code'] == sku]
        if not m_row.empty:
            desc = m_row['DESCRIPTION'].iloc[0]
            print(f"  Master Desc: {desc}")
        print()
