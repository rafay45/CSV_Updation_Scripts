#!/usr/bin/env python3
"""
Generate SQL INSERT statements for Mixed profiles
Format: MIXED-{RAIL}-RAIL-{GAP}-GAP-{BODY}-BODY-{WIDTH}-WIDE-2.pdf
Example: MIXED-3-RAIL-1-GAP-37-BODY-6-WIDE-2.pdf
"""

import os
import re

# Configuration
PROFILES_DIR = r"C:\Users\user\Downloads\CSVUpdationScripts\profiles"
BASE_URL = "https://staging2.wholesalefencing.com/wp-content/uploads/profiles/"
CATEGORY_ID = 3606  # Horizontal Aluminum category

# GAP mapping for database
GAP_MAPPING = {
    '12': '1-2',      # 1/2 GAP
    '1': '1',         # 1 GAP
    '138': '1-3-8',   # 1 3/8 GAP
    '1-38': '1-3-8'   # 1 3/8 GAP (alternate format)
}

# RAIL mapping for database
RAIL_MAPPING = {
    '3': '3-rail',
    '4': '4-rail'
}

def convert_body_value(body_str):
    """Convert body filename format to database format
    Examples:
    - '37' -> '37'
    - '40-12' -> '40-1-2'
    - '34.5' -> '34-1-2'
    """
    if '.' in body_str:
        # Handle decimal format like '34.5'
        parts = body_str.split('.')
        if parts[1] == '5':
            return f"{parts[0]}-1-2"
        return body_str
    elif '-12' in body_str:
        # Handle format like '40-12' -> '40-1-2'
        return body_str.replace('-12', '-1-2')
    else:
        # Plain number like '37'
        return body_str

def generate_sql():
    """Generate SQL INSERT statements for all Mixed PDFs"""

    sql_statements = []
    sql_statements.append("-- MIXED Profiles for Horizontal Aluminum (Category ID: 3606)")
    sql_statements.append("-- RAIL + GAP + WIDTH + BODY combinations\n")

    # Get all MIXED PDFs
    files = [f for f in os.listdir(PROFILES_DIR) if f.startswith('MIXED') and f.endswith('-2.pdf') and ' (1)' not in f]
    files.sort()

    print(f"Found {len(files)} Mixed PDF files")

    # Group by RAIL first, then GAP
    rail_groups = {}

    for filename in files:
        # Parse filename: MIXED-{RAIL}-RAIL-{GAP}-GAP-{BODY}-BODY-{WIDTH}-WIDE-2.pdf
        # Examples:
        # MIXED-3-RAIL-1-GAP-37-BODY-6-WIDE-2.pdf
        # MIXED-4-RAIL-1-GAP-34-12-BODY-8-WIDE-2.pdf

        match = re.match(r'MIXED-(\d+)-RAIL-([\d\-]+)-GAP-([\d\.\-]+)-BODY-(\d+)-WIDE-2\.pdf', filename)

        if match:
            rail_file = match.group(1)      # '3' or '4'
            gap_file = match.group(2)       # '1', '12', etc.
            body_file = match.group(3)      # '37', '40-12', etc.
            width = match.group(4)          # '6' or '8'

            # Convert to database format
            rail_db = RAIL_MAPPING.get(rail_file, rail_file + '-rail')
            gap_db = GAP_MAPPING.get(gap_file, gap_file)
            body_db = convert_body_value(body_file)

            # Create SQL statement
            pdf_url = BASE_URL + filename

            sql = f"INSERT INTO wpg0_profiles (category_id, body_height, gap, rail_size, panel_width, pdf_url, created_at) VALUES ({CATEGORY_ID}, '{body_db}', '{gap_db}', '{rail_db}', '{width}', '{pdf_url}', NOW());"

            # Group by RAIL, then GAP for organization
            if rail_db not in rail_groups:
                rail_groups[rail_db] = {}

            if gap_db not in rail_groups[rail_db]:
                rail_groups[rail_db][gap_db] = []

            rail_groups[rail_db][gap_db].append((body_db, width, sql))

        else:
            print(f"Warning: Could not parse filename: {filename}")

    # Generate organized SQL output
    rail_order = {'3-rail': 1, '4-rail': 2}
    gap_order = {'1-2': 1, '1': 2, '1-3-8': 3}

    for rail in sorted(rail_groups.keys(), key=lambda x: rail_order.get(x, 99)):
        sql_statements.append(f"\n-- RAIL = {rail}")

        for gap in sorted(rail_groups[rail].keys(), key=lambda x: gap_order.get(x, 99)):
            sql_statements.append(f"-- GAP = {gap}")

            # Sort by body height, then width
            def body_to_number(body_str):
                parts = body_str.split('-')
                if len(parts) == 1:
                    return float(parts[0])
                elif len(parts) == 3:
                    return float(parts[0]) + float(parts[1]) / float(parts[2])
                return float(body_str.replace('-', '.'))

            rail_groups[rail][gap].sort(key=lambda x: (body_to_number(x[0]), int(x[1])))

            for body_db, width, sql in rail_groups[rail][gap]:
                sql_statements.append(sql)

    return '\n'.join(sql_statements)

if __name__ == '__main__':
    sql_content = generate_sql()

    output_file = r"C:\Users\user\Downloads\CSVUpdationScripts\insert-mixed-profiles.sql"

    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(sql_content)

    print(f"\nSQL file generated: {output_file}")
    print(f"Total lines: {len(sql_content.splitlines())}")
