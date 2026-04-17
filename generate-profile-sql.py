#!/usr/bin/env python3
"""
Generate SQL INSERT statements for profile PDFs
Parses PDF filenames and creates database entries
"""

import os
import re
from pathlib import Path

# Configuration
CATEGORY_ID = 18524  # Privacy category ID - CHANGE THIS IF NEEDED
PDF_FOLDER = r"C:\Users\user\Downloads\CSVUpdationScripts\profiles"
BASE_URL = "https://staging2.wholesalefencing.com/wp-content/uploads/profiles/"
OUTPUT_FILE = r"C:\Users\user\Downloads\CSVUpdationScripts\insert-all-profiles.sql"

def parse_filename(filename):
    """
    Parse PDF filename to extract filter values
    Example: "20-Body-6-Wide-11.3-Picket-1.5-x-5.5-Rail.pdf"
    Returns: (body_height, panel_width, picket_size, rail_size)
    """
    # Remove .pdf extension
    name = filename.replace('.pdf', '')

    # Extract Body (height)
    body_match = re.search(r'(\d+)-Body', name)
    body_height = body_match.group(1) if body_match else None

    # Extract Panel Width (comes before "Wide")
    width_match = re.search(r'[-–]\s*(\d+)\s*-?Wide', name)
    panel_width = width_match.group(1) if width_match else None

    # Extract Picket Size
    picket_match = re.search(r'(\d+(?:\.\d+)?)\s*-?Picket', name)
    picket_size = picket_match.group(1) if picket_match else None

    # Extract Rail Size (various formats)
    # Try: "1.5-x-5.5-Rail", "1.5x5.5-Rail", "1.5x-5.5-Rail", "2x7-Rail"
    rail_match = re.search(r'(\d+(?:\.\d+)?)\s*x?-?\s*x?-?\s*(\d+(?:\.\d+)?)\s*-?Rail', name)
    if rail_match:
        rail_size = f"{rail_match.group(1)}-x-{rail_match.group(2)}"
    else:
        # Try simpler pattern like "2x7"
        rail_match2 = re.search(r'(\d+)x(\d+)', name)
        if rail_match2:
            rail_size = f"{rail_match2.group(1)}-x-{rail_match2.group(2)}"
        else:
            rail_size = None

    return body_height, panel_width, picket_size, rail_size

def generate_sql():
    """Generate SQL INSERT statements for all PDFs"""

    pdf_files = [f for f in os.listdir(PDF_FOLDER) if f.endswith('.pdf')]

    print(f"Found {len(pdf_files)} PDF files")
    print(f"Generating SQL for category ID: {CATEGORY_ID}")
    print("-" * 60)

    sql_lines = []
    sql_lines.append("-- Auto-generated SQL for profile PDFs")
    sql_lines.append(f"-- Total PDFs: {len(pdf_files)}")
    sql_lines.append(f"-- Category ID: {CATEGORY_ID}")
    sql_lines.append("")

    successful = 0
    failed = 0

    for pdf_file in sorted(pdf_files):
        body, width, picket, rail = parse_filename(pdf_file)

        # Skip if couldn't parse essential fields
        if not all([body, width, picket, rail]):
            print(f"WARNING: Skipped (couldn't parse): {pdf_file}")
            print(f"   Body={body}, Width={width}, Picket={picket}, Rail={rail}")
            failed += 1
            continue

        # Convert rail format: "1-x-5.5" -> "1-5-x-5-5"
        rail_formatted = rail.replace('.', '-')

        # Build SQL INSERT
        pdf_url = BASE_URL + pdf_file.replace(' ', '%20')  # URL encode spaces

        sql = f"INSERT INTO `wpg0_profiles` (`category_id`, `body_height`, `picket_size`, `rail_size`, `panel_width`, `pdf_url`) VALUES ({CATEGORY_ID}, '{body}', '{picket}', '{rail_formatted}', '{width}', '{pdf_url}');"

        sql_lines.append(sql)
        successful += 1

        print(f"OK: {pdf_file}")
        print(f"  Body={body}\", Picket={picket}\", Rail={rail_formatted}, Width={width}'")

    # Write to file
    with open(OUTPUT_FILE, 'w', encoding='utf-8') as f:
        f.write('\n'.join(sql_lines))

    print("-" * 60)
    print(f"SUCCESS: {successful} PDFs processed")
    print(f"FAILED: {failed} PDFs skipped")
    print(f"\nSQL file created: {OUTPUT_FILE}")
    print(f"\nNext steps:")
    print(f"1. Open phpMyAdmin")
    print(f"2. Select wpg0_profiles table")
    print(f"3. Go to SQL tab")
    print(f"4. Copy-paste content from {OUTPUT_FILE}")
    print(f"5. Click Go!")

if __name__ == "__main__":
    generate_sql()
