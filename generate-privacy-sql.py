#!/usr/bin/env python3
"""
Generate SQL INSERT statements for Privacy profiles
Format: FULL-PRIVACY-{BODY}-BODY-{WIDTH}-WIDE-2.pdf
Example: FULL-PRIVACY-35-BODY-6-WIDE-2.pdf
"""

import os
import re

# Configuration
PROFILES_DIR = r"C:\Users\user\Downloads\CSVUpdationScripts\profiles"
BASE_URL = "https://staging2.wholesalefencing.com/wp-content/uploads/profiles/"
CATEGORY_ID = 3606  # Horizontal Aluminum category

def convert_body_value(body_str):
    """Convert body filename format to database format
    Examples:
    - '35' -> '35'
    - '38-12' -> '38-1-2'
    """
    if '-12' in body_str:
        # Handle format like '38-12' -> '38-1-2'
        return body_str.replace('-12', '-1-2')
    else:
        # Plain number like '35'
        return body_str

def generate_sql():
    """Generate SQL INSERT statements for all Privacy PDFs"""

    sql_statements = []
    sql_statements.append("-- FULL-PRIVACY Profiles for Horizontal Aluminum (Category ID: 3606)")
    sql_statements.append("-- WIDTH + BODY combinations\n")

    # Get all FULL-PRIVACY PDFs
    files = [f for f in os.listdir(PROFILES_DIR) if f.startswith('FULL-PRIVACY') and f.endswith('-2.pdf') and ' (1)' not in f]
    files.sort()

    print(f"Found {len(files)} Privacy PDF files")

    # Store records for sorting
    records = []

    for filename in files:
        # Parse filename: FULL-PRIVACY-{BODY}-BODY-{WIDTH}-WIDE-2.pdf
        # Examples:
        # FULL-PRIVACY-35-BODY-6-WIDE-2.pdf
        # FULL-PRIVACY-38-12-BODY-8-WIDE-2.pdf

        match = re.match(r'FULL-PRIVACY-([\d\-]+)-BODY-(\d+)-WIDE-2\.pdf', filename)

        if match:
            body_file = match.group(1)      # '35', '38-12', etc.
            width = match.group(2)          # '6' or '8'

            # Convert to database format
            body_db = convert_body_value(body_file)

            # Create SQL statement
            pdf_url = BASE_URL + filename

            sql = f"INSERT INTO wpg0_profiles (category_id, body_height, panel_width, pdf_url, created_at) VALUES ({CATEGORY_ID}, '{body_db}', '{width}', '{pdf_url}', NOW());"

            records.append((body_db, width, sql))

        else:
            print(f"Warning: Could not parse filename: {filename}")

    # Sort by body height, then width
    def body_to_number(body_str):
        parts = body_str.split('-')
        if len(parts) == 1:
            return float(parts[0])
        elif len(parts) == 3:
            return float(parts[0]) + float(parts[1]) / float(parts[2])
        return float(body_str.replace('-', '.'))

    records.sort(key=lambda x: (body_to_number(x[0]), int(x[1])))

    for body_db, width, sql in records:
        sql_statements.append(sql)

    return '\n'.join(sql_statements)

if __name__ == '__main__':
    sql_content = generate_sql()

    output_file = r"C:\Users\user\Downloads\CSVUpdationScripts\insert-privacy-profiles.sql"

    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(sql_content)

    print(f"\nSQL file generated: {output_file}")
    print(f"Total lines: {len(sql_content.splitlines())}")
