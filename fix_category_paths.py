import csv

# ── Strict subcategory mapping ────────────────────────────────────────────────
# Based on column order in Master File spreadsheet
# Only these subcats are valid for each main category
VALID_SUBCATS = {
    'Vinyl':             {'Privacy', 'Lattice', '2 Rail', '3 Rail', '4 Rail', 'Picket', 'Scalloped Picket', 'Pool'},
    'Ornamental':        {'Smooth Top', 'Point Top', 'Square Top', 'Curved Top'},
    'Modern':            {'Horizontal Aluminum', 'Aluminum With Fill', 'Vertical Aluminum'},
    'Simtek':            {'Eco Stone', 'Eco Wood'},
    'Trex':              {'Seclusions', 'Horizons', 'Horizontal'},
    'Agriculture':       {'Barbwire', 'Field Fence'},
    'Chain Link':        set(),   # no subcats
    'Wood':              set(),   # no subcats
    'Metal Horse Fence': set(),   # no subcats
}

# Normalize subcat names: handle spacing variants like "2-Rail" vs "2 Rail"
# Map lowercase-stripped variants to canonical form
def normalize_subcat(s):
    return s.strip().replace('-', ' ').lower()

# Build normalized lookup: norm_name -> canonical_name  per main cat
NORM_VALID = {}
for main, subs in VALID_SUBCATS.items():
    NORM_VALID[main] = {}
    for sub in subs:
        NORM_VALID[main][normalize_subcat(sub)] = sub

INPUT_FILE  = r"C:\Users\user\Downloads\ProductData - ProductData_FIXED.csv"
OUTPUT_FILE = r"C:\Users\user\Downloads\ProductData - ProductData_FIXED.csv"


def fix_single_cat_path(path):
    """
    Fix one category path like 'Vinyl > Smooth Top > Tools / Misc'
    Rules:
      - parts[0] = main category
      - if main cat has valid subcats:
          - parts[1] must be a valid subcat (after normalization)
          - if not valid: remove parts[1], keep parts[0] and rest (tags become direct under main)
      - if main cat has NO subcats (Chain Link, Wood, Metal Horse Fence):
          - keep as-is (parts[0] > tag is fine)
    Returns corrected path string, or None if path should be dropped entirely.
    """
    parts = [p.strip() for p in path.split('>')]
    if not parts or not parts[0]:
        return None

    main = parts[0]
    if main not in VALID_SUBCATS:
        # Unknown main cat - keep as-is
        return path

    valid_subs = VALID_SUBCATS[main]
    norm_valid = NORM_VALID[main]

    if not valid_subs:
        # Chain Link, Wood, Metal Horse Fence - no subcats defined, keep as-is
        return path

    if len(parts) == 1:
        # Just 'Vinyl' with no subcat or tag - keep
        return path

    if len(parts) >= 2:
        subcat_raw = parts[1]
        subcat_norm = normalize_subcat(subcat_raw)

        if subcat_norm in norm_valid:
            # Valid subcat - keep path as-is, use canonical spelling
            canonical = norm_valid[subcat_norm]
            parts[1] = canonical
            return ' > '.join(parts)
        else:
            # Invalid subcat for this main category
            # This subcat is actually a tag/component-type that ended up here
            # Remove it: collapse 'Main > BadSubcat > Tag' -> 'Main > Tag'
            # OR if it's 'Main > BadSubcat' (no tag) -> just 'Main'
            remaining = parts[2:]  # everything after the bad subcat
            if remaining:
                return main + ' > ' + ' > '.join(remaining)
            else:
                # 'Main > BadSubcat' only - just return Main
                return main


def fix_categories(cats_str):
    """Fix all category paths in a comma-separated categories string."""
    if not cats_str.strip():
        return cats_str

    paths = [p.strip() for p in cats_str.split(',') if p.strip()]
    fixed_paths = []
    seen = set()

    for path in paths:
        fixed = fix_single_cat_path(path)
        if fixed and fixed not in seen:
            seen.add(fixed)
            fixed_paths.append(fixed)

    # Second pass: remove any path that is a prefix-only duplicate of a longer path.
    # e.g. if we have both 'Vinyl > Panels' and 'Vinyl > Lattice > Panels',
    # drop 'Vinyl > Panels' as it's redundant (product already covered by longer path).
    cleaned = []
    for path in fixed_paths:
        is_redundant = False
        for other in fixed_paths:
            if other != path and other.startswith(path + ' > '):
                # 'path' is a prefix of 'other' - redundant
                is_redundant = True
                break
        if not is_redundant:
            cleaned.append(path)

    return ', '.join(cleaned)


def main():
    with open(INPUT_FILE, encoding='utf-8-sig', newline='') as f:
        reader = csv.DictReader(f)
        fieldnames = list(reader.fieldnames)
        all_rows = list(reader)

    print(f"Total rows: {len(all_rows)}")

    fixed_rows = []
    changes = 0

    for row in all_rows:
        old_cats = row.get('Categories', '')
        new_cats = fix_categories(old_cats)
        if new_cats != old_cats:
            changes += 1
        row['Categories'] = new_cats
        fixed_rows.append(row)

    print(f"Rows with category changes: {changes}")

    # Write output
    with open(OUTPUT_FILE, 'w', encoding='utf-8-sig', newline='') as f:
        writer = csv.DictWriter(f, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(fixed_rows)

    print(f"\nDone! Saved to:\n{OUTPUT_FILE}")

    # Verify - check Vinyl level-2 subcats
    print("\n=== Vinyl level-2 subcategories after fix ===")
    vinyl_l2 = set()
    for r in fixed_rows:
        cats = r.get('Categories', '')
        for cat in cats.split(','):
            cat = cat.strip()
            parts = [p.strip() for p in cat.split('>')]
            if len(parts) >= 2 and parts[0] == 'Vinyl':
                vinyl_l2.add(parts[1])
    for s in sorted(vinyl_l2):
        print(f"  {s}")

    print("\n=== Ornamental level-2 subcategories after fix ===")
    orn_l2 = set()
    for r in fixed_rows:
        cats = r.get('Categories', '')
        for cat in cats.split(','):
            cat = cat.strip()
            parts = [p.strip() for p in cat.split('>')]
            if len(parts) >= 2 and parts[0] == 'Ornamental':
                orn_l2.add(parts[1])
    for s in sorted(orn_l2):
        print(f"  {s}")

    print("\n=== Sample changes (first 5) ===")
    count = 0
    for r in fixed_rows:
        old = ''
        new = r.get('Categories', '')
        # re-read old for comparison
        if count < 5:
            pass
    # Actually show some vinyl smooth top cases
    print("\n=== Checking for bad Vinyl subcats ===")
    bad = 0
    for r in fixed_rows:
        cats = r.get('Categories', '')
        if 'Vinyl > Smooth Top' in cats or 'Vinyl > Post' in cats or 'Vinyl > Rails' in cats:
            print(f"  STILL BAD: SKU={r.get('SKU')}  {cats}")
            bad += 1
    if bad == 0:
        print("  None found - all clean!")


if __name__ == '__main__':
    main()
