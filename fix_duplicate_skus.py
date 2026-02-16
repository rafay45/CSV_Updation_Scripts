import csv
import os
from collections import OrderedDict

INPUT_FILE  = r"C:\Users\user\Downloads\ProductData - ProductData.csv"
OUTPUT_FILE = r"C:\Users\user\Downloads\ProductData - ProductData_FIXED.csv"

def fix_csv():
    # ── 1. Read all rows ──────────────────────────────────────────────────────
    with open(INPUT_FILE, encoding='utf-8-sig', newline='') as f:
        reader = csv.DictReader(f)
        fieldnames = reader.fieldnames
        all_rows = list(reader)

    print(f"Total rows in input  : {len(all_rows)}")

    # ── 2. Group rows by SKU ──────────────────────────────────────────────────
    sku_groups = OrderedDict()
    no_sku_rows = []

    for row in all_rows:
        sku = row.get('SKU', '').strip()
        if not sku:
            no_sku_rows.append(row)
            continue
        if sku not in sku_groups:
            sku_groups[sku] = []
        sku_groups[sku].append(row)

    print(f"Unique SKUs          : {len(sku_groups)}")
    print(f"Rows without SKU     : {len(no_sku_rows)}")
    dupes = {k: v for k, v in sku_groups.items() if len(v) > 1}
    print(f"SKUs with duplicates : {len(dupes)}")

    # ── 3. Merge duplicates ───────────────────────────────────────────────────
    merged_rows = []

    for sku, rows in sku_groups.items():
        if len(rows) == 1:
            merged_rows.append(rows[0])
            continue

        # Base row = first occurrence (all fields same except Categories & Tags)
        base = dict(rows[0])

        # Collect ALL unique categories across duplicate rows
        all_cats = []
        seen_cats = set()
        for r in rows:
            cats = r.get('Categories', '').strip()
            if cats and cats not in seen_cats:
                seen_cats.add(cats)
                all_cats.append(cats)

        # Collect ALL unique tags across duplicate rows
        all_tags = []
        seen_tags = set()
        for r in rows:
            tags_val = r.get('Tags', '').strip()
            if tags_val:
                for t in [x.strip() for x in tags_val.split(',')]:
                    if t and t not in seen_tags:
                        seen_tags.add(t)
                        all_tags.append(t)

        base['Categories'] = ', '.join(all_cats)
        base['Tags']       = ', '.join(all_tags) if all_tags else rows[0].get('Tags', '')

        merged_rows.append(base)

    # Add rows that had no SKU at end (keep them as-is)
    merged_rows.extend(no_sku_rows)

    print(f"Total rows in output : {len(merged_rows)}")

    # ── 4. Write output CSV ───────────────────────────────────────────────────
    with open(OUTPUT_FILE, 'w', encoding='utf-8-sig', newline='') as f:
        writer = csv.DictWriter(f, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(merged_rows)

    print(f"\nDone! Fixed file saved to:\n{OUTPUT_FILE}")

    # ── 5. Show summary of what was merged ───────────────────────────────────
    print("\n── Top 10 merged SKUs ──────────────────────────────────────────────")
    for sku, rows in sorted(dupes.items(), key=lambda x: -len(x[1]))[:10]:
        cats = set(r.get('Categories','') for r in rows)
        name = rows[0].get('Name','')[:60]
        print(f"  SKU {sku} ({len(rows)} rows) -> {len(cats)} categories")
        print(f"    Name: {name}")
        for c in sorted(cats):
            print(f"    Cat : {c}")
        print()

if __name__ == '__main__':
    fix_csv()
