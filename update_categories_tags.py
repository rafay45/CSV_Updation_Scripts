import csv

# Read from FIXED file (jo pehle bani thi - correct categories, original tags)
# Output: new file with:
#   1. Categories: MainCat > OldTag > ComponentType
#   2. Tags: ComponentType value
#   3. Attribute 6 removed, 7-40 renumbered to 6-39

INPUT_FILE  = r"C:\Users\user\Downloads\ProductData - ProductData_FIXED.csv"
OUTPUT_FILE = r"C:\Users\user\Downloads\ProductData - ProductData_FIXED.csv"

def update_csv():
    with open(INPUT_FILE, encoding='utf-8-sig', newline='') as f:
        reader = csv.DictReader(f)
        original_fieldnames = list(reader.fieldnames)
        all_rows = list(reader)

    print(f"Total rows: {len(all_rows)}")

    # Check if Attribute 6 is still Component Type or already removed
    sample = all_rows[0]
    attr6_name = sample.get('Attribute 6 name', '').strip()
    print(f"Attribute 6 name in file: '{attr6_name}'")

    # Check current Tags value to understand state
    print(f"Sample Tags: '{sample.get('Tags','')}'")
    print(f"Sample Categories: '{sample.get('Categories','')}'")

    # Determine if file is already modified or original state
    # If Attribute 6 = "Component Type" -> original state, process normally
    # If Attribute 6 = "Product Part" -> Attribute 6 was already removed in previous run

    if attr6_name == 'Component Type':
        COMP_TYPE_COL = 'Attribute 6 value(s)'
        REMOVE_COLS = {'Attribute 6 name', 'Attribute 6 value(s)', 'Attribute 6 visible'}
        RENUMBER_FROM = 7
    else:
        # Already removed - Tags column now has ComponentType from last run
        # We cannot recover original tags - need to re-run from scratch
        print("ERROR: Attribute 6 (Component Type) already removed from file!")
        print("Please re-run fix_duplicate_skus.py first to regenerate FIXED file.")
        return

    # Build new fieldnames - remove Attr6, renumber 7-40 -> 6-39
    remove_cols = REMOVE_COLS

    def rename_attr(col):
        for n in range(40, 6, -1):
            old_prefix = f'Attribute {n} '
            new_prefix = f'Attribute {n-1} '
            if col.startswith(old_prefix):
                return new_prefix + col[len(old_prefix):]
            if col == f'Attribute {n}':
                return f'Attribute {n-1}'
        return col

    new_fieldnames = []
    for col in original_fieldnames:
        if col in remove_cols:
            continue
        new_fieldnames.append(rename_attr(col))

    # Process rows
    updated_rows = []
    for row in all_rows:
        component_type = row.get(COMP_TYPE_COL, '').strip()
        old_tag        = row.get('Tags', '').strip()   # e.g. "Pipe", "Gate Hardware"
        old_cats       = row.get('Categories', '').strip()

        # ── Build new Categories ──────────────────────────────────────────────
        if old_cats and component_type:
            cat_parts = [c.strip() for c in old_cats.split(',') if c.strip()]
            new_cat_parts = []
            for cat in cat_parts:
                if '>' not in cat:
                    # Simple main category like "Chain Link"
                    if old_tag:
                        new_cat_parts.append(f"{cat} > {old_tag} > {component_type}")
                    else:
                        new_cat_parts.append(f"{cat} > {component_type}")
                else:
                    # Already has subcategory like "Ornamental > Smooth Top"
                    # Just append component type at end
                    new_cat_parts.append(f"{cat} > {component_type}")
            new_cats = ', '.join(new_cat_parts)
        elif old_cats:
            new_cats = old_cats
        else:
            new_cats = ''

        # ── Build new row (skip Attr6, renumber rest) ─────────────────────────
        new_row = {}
        for col in original_fieldnames:
            if col in remove_cols:
                continue
            new_key = rename_attr(col)
            new_row[new_key] = row.get(col, '')

        # Override Categories and Tags
        new_row['Categories'] = new_cats
        new_row['Tags']       = component_type  # set to Component Type

        updated_rows.append(new_row)

    # Write output
    with open(OUTPUT_FILE, 'w', encoding='utf-8-sig', newline='') as f:
        writer = csv.DictWriter(f, fieldnames=new_fieldnames)
        writer.writeheader()
        writer.writerows(updated_rows)

    print(f"\nDone! Saved to:\n{OUTPUT_FILE}")

    # Sample output
    print("\n=== Sample (first 5 rows) ===")
    for r in updated_rows[:5]:
        print(f"  SKU       : {r.get('SKU')}")
        print(f"  Categories: {r.get('Categories')}")
        print(f"  Tags      : {r.get('Tags')}")
        print(f"  Attr 5    : {r.get('Attribute 5 name')} = {r.get('Attribute 5 value(s)')}")
        print(f"  Attr 6    : {r.get('Attribute 6 name')} = {r.get('Attribute 6 value(s)')}")
        print()

    # Multi-category example
    print("=== Multi-category example ===")
    for r in updated_rows:
        if ',' in r.get('Categories',''):
            print(f"  SKU       : {r.get('SKU')}")
            print(f"  Categories: {r.get('Categories')}")
            print(f"  Tags      : {r.get('Tags')}")
            print()
            break

if __name__ == '__main__':
    update_csv()
