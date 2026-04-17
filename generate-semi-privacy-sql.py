#!/usr/bin/env python3
"""
Generate SQL INSERT statements for Semi-Privacy profiles
Format: SEMI-PRIVACY-{GAP}-GAP-{BODY}-BODY-{WIDTH}-WIDE-2.pdf
Example: SEMI-PRIVACY-12-GAP-35-12-BODY-6-WIDE-2.pdf
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

def convert_body_value(body_str):
    """Convert body filename format to database format
    Examples:
    - '35' -> '35'
    - '39-12' -> '39-1-2'
    - '102.5' -> '102-1-2'
    - '37-58' -> '37-5-8'
    - '52-14' -> '52-1-4'
    """
    if '.' in body_str:
        # Handle decimal format like '102.5'
        parts = body_str.split('.')
        if parts[1] == '5':
            return f"{parts[0]}-1-2"
        return body_str
    elif '-12' in body_str:
        # Handle format like '39-12' -> '39-1-2'
        return body_str.replace('-12', '-1-2')
    elif '-58' in body_str:
        # Handle format like '37-58' -> '37-5-8'
        return body_str.replace('-58', '-5-8')
    elif '-38' in body_str:
        # Handle format like '47-38' -> '47-3-8'
        return body_str.replace('-38', '-3-8')
    elif '-14' in body_str:
        # Handle format like '52-14' -> '52-1-4'
        return body_str.replace('-14', '-1-4')
    elif '-18' in body_str:
        # Handle format like '57-18' -> '57-1-8'
        return body_str.replace('-18', '-1-8')
    elif '-78' in body_str:
        # Handle format like '66-78' -> '66-7-8'
        return body_str.replace('-78', '-7-8')
    elif '-34' in body_str:
        # Handle format like '71-34' -> '71-3-4'
        return body_str.replace('-34', '-3-4')
    else:
        # Plain number like '35'
        return body_str

def generate_sql():
    """Generate SQL INSERT statements for all Semi-Privacy PDFs"""

    sql_statements = []
    sql_statements.append("-- SEMI-PRIVACY Profiles for Horizontal Aluminum (Category ID: 3606)")
    sql_statements.append("-- GAP + WIDTH + BODY combinations\n")

    # Get all SEMI-PRIVACY PDFs
    files = [f for f in os.listdir(PROFILES_DIR) if f.startswith('SEMI-PRIVACY') and f.endswith('-2.pdf') and ' (1)' not in f]
    files.sort()

    print(f"Found {len(files)} Semi-Privacy PDF files")

    # Group by GAP
    gap_groups = {}

    for filename in files:
        # Parse filename: SEMI-PRIVACY-{GAP}-GAP-{BODY}-BODY-{WIDTH}-WIDE-2.pdf
        # Examples:
        # SEMI-PRIVACY-12-GAP-35-12-BODY-6-WIDE-2.pdf
        # SEMI-PRIVACY-1-GAP-35-BODY-6-WIDE-2.pdf
        # SEMI-PRIVACY-1-GAP-102.5-BODY-6-WIDE-2.pdf
        # SEMI-PRIVACY-1-38-GAP-37-58-BODY-6-WIDE-2.pdf

        match = re.match(r'SEMI-PRIVACY-([\d\-]+)-GAP-([\d\.\-]+)-BODY-(\d+)-WIDE-2\.pdf', filename)

        if match:
            gap_file = match.group(1)      # '12', '1', or '138'
            body_file = match.group(2)      # '35', '39-12', '102.5', etc.
            width = match.group(3)          # '6' or '8'

            # Convert to database format
            gap_db = GAP_MAPPING.get(gap_file, gap_file)
            body_db = convert_body_value(body_file)

            # Create SQL statement
            pdf_url = BASE_URL + filename

            sql = f"INSERT INTO wpg0_profiles (category_id, body_height, gap, panel_width, pdf_url, created_at) VALUES ({CATEGORY_ID}, '{body_db}', '{gap_db}', '{width}', '{pdf_url}', NOW());"

            # Group by GAP for organization
            if gap_db not in gap_groups:
                gap_groups[gap_db] = []
            gap_groups[gap_db].append((body_db, width, sql))

        else:
            print(f"Warning: Could not parse filename: {filename}")

    # Generate organized SQL output
    gap_order = {'1-2': 1, '1': 2, '1-3-8': 3}
    for gap in sorted(gap_groups.keys(), key=lambda x: gap_order.get(x, 99)):
        sql_statements.append(f"\n-- GAP = {gap}")

        # Sort by body height, then width
        # Convert body format '35-1-2' to sortable number for comparison
        def body_to_number(body_str):
            parts = body_str.split('-')
            if len(parts) == 1:
                return float(parts[0])
            elif len(parts) == 3:
                return float(parts[0]) + float(parts[1]) / float(parts[2])
            return float(body_str.replace('-', '.'))

        gap_groups[gap].sort(key=lambda x: (body_to_number(x[0]), int(x[1])))

        for body_db, width, sql in gap_groups[gap]:
            sql_statements.append(sql)

    return '\n'.join(sql_statements)

if __name__ == '__main__':
    sql_content = generate_sql()

    output_file = r"C:\Users\user\Downloads\CSVUpdationScripts\insert-semi-privacy-profiles.sql"

    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(sql_content)

    print(f"\nSQL file generated: {output_file}")
    print(f"Total lines: {len(sql_content.splitlines())}")
