import csv

INPUT_FILE  = r"C:\Users\user\Downloads\ProductData - ProductData_FIXED.csv"
OUTPUT_FILE = r"C:\Users\user\Downloads\ProductData - ProductData_FIXED.csv"


def process(cats_str, tag_str):
    """
    For each category path, append tag as a deeper level.
    e.g.  'Chain Link > Pipe'  +  tag='Top Rail'
          -> 'Chain Link > Pipe > Top Rail'

    Tags column becomes empty after this.
    """
    tag = tag_str.strip()
    if not tag:
        # No tag — categories unchanged, tags stay empty
        return cats_str, ''

    if not cats_str.strip():
        # No category either — nothing to do
        return cats_str, ''

    paths = [p.strip() for p in cats_str.split(',') if p.strip()]
    new_paths = []
    seen = set()

    for path in paths:
        new_path = path + ' > ' + tag
        if new_path not in seen:
            seen.add(new_path)
            new_paths.append(new_path)

    return ', '.join(new_paths), ''   # tags cleared


def main():
    with open(INPUT_FILE, encoding='utf-8-sig', newline='') as f:
        reader = csv.DictReader(f)
        fieldnames = list(reader.fieldnames)
        all_rows = list(reader)

    print(f"Total rows: {len(all_rows)}")

    changed = 0
    for row in all_rows:
        old_cats = row.get('Categories', '')
        old_tags = row.get('Tags', '')

        new_cats, new_tags = process(old_cats, old_tags)

        if new_cats != old_cats or new_tags != old_tags:
            changed += 1

        row['Categories'] = new_cats
        row['Tags']       = new_tags

    print(f"Rows changed: {changed}")

    with open(OUTPUT_FILE, 'w', encoding='utf-8-sig', newline='') as f:
        writer = csv.DictWriter(f, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(all_rows)

    print(f"\nDone! Saved to:\n{OUTPUT_FILE}")

    # Show sample output
    print("\n=== Sample output (first 10 changed rows) ===")
    count = 0
    for row in all_rows:
        if row.get('Tags', '') == '' and row.get('Categories', ''):
            if count < 10:
                print(f"  SKU: {row.get('SKU','')}")
                print(f"  Categories: {row.get('Categories','')}")
                print(f"  Tags: '{row.get('Tags','')}'")
                print()
            count += 1

    # Show unique level-3 categories sample
    print("=== Unique category patterns (first 20) ===")
    patterns = set()
    for row in all_rows:
        for cat in row.get('Categories', '').split(','):
            cat = cat.strip()
            if cat:
                patterns.add(cat)
    for p in sorted(patterns)[:20]:
        print(f"  {p}")


if __name__ == '__main__':
    main()
