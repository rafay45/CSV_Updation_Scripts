import os
import re

# Category ID mapping for staging site
CATEGORY_MAP = {
    'vinyl-privacy': 3535,
    'vinyl-2-rail': 3541,
    'vinyl-3-rail': 3544,
    'vinyl-4-rail': 3547,
    'vinyl-picket-fence': 3550,
    'vinyl-pool-fence': 3556,
    'vinyl-privacy-with-lattice-top': 3538,
    'vinyl-scalloped-picket': 3553,
    'aluminum-with-fill': 3609,
    'horizontal-aluminum': 3606
}

def normalize_value(val):
    """Normalize values for database storage"""
    val = val.strip().replace('"', '').replace("'", '')
    # Remove extra spaces and dashes first
    val = re.sub(r'[\s–-]+', ' ', val)
    # Handle decimal points
    val = val.replace('.', '-')
    # Normalize x to -x-
    val = re.sub(r'\s*x\s*', '-x-', val, flags=re.IGNORECASE)
    # Replace remaining spaces with dashes
    val = val.replace(' ', '-')
    # Remove trailing dashes
    val = val.rstrip('-')
    return val.lower()

def parse_privacy_filename(filename):
    """
    Parse Privacy pattern: {body}-Body-{width}-Wide-{picket}-Picket-{rail}-Rail.pdf
    Examples:
    - 20-Body-6-Wide-11.3-Picket-1.5-x-5.5-Rail.pdf
    - 24-Body-6-Wide-6-Picket-2-x-7-Rail.pdf
    """
    # Handle both standard and with dashes patterns
    pattern = r'^(\d+)[\s_-]*Body[\s–-]+(\d+)[\s_-]*Wide[\s–-]+([0-9.]+)[\s_-]*Picket[\s–-]+([0-9.x\s-]+)[\s_-]*Rail'
    match = re.search(pattern, filename, re.IGNORECASE)

    if match:
        body = match.group(1)
        width = match.group(2)
        picket = match.group(3)
        rail = normalize_value(match.group(4))

        return {
            'category_id': CATEGORY_MAP['vinyl-privacy'],
            'body_height': body,
            'picket_size': picket,
            'rail_size': rail,
            'panel_width': width,
            'filename': filename
        }
    return None

def parse_lattice_filename(filename):
    """
    Parse Lattice pattern: {body}-Body-{width}-Wide-{picket}-Picket-{rail}-Rail-Lattice-Window-{lattice_height}-{lattice_rail}-Rail.pdf
    Examples:
    - 68-Body-6-Wide-11.3-Picket-1.5-x-5.5-Rail-Lattice-Window-8.5-3.5-Rail.pdf
    """
    pattern = r'^(\d+)[\s_-]*Body[\s–-]+(\d+)[\s_-]*Wide[\s–-]+([0-9.]+)[\s_-]*Picket[\s–-]+([0-9.x\s-]+)[\s_-]*Rail[\s–-]+Lattice[\s–-]+Window[\s–-]+([0-9.]+)[\s–-]+([0-9.x\s-]+)[\s_-]*Rail'
    match = re.search(pattern, filename, re.IGNORECASE)

    if match:
        body = match.group(1)
        width = match.group(2)
        picket = match.group(3)
        rail = normalize_value(match.group(4))
        lattice_height = match.group(5)
        lattice_rail_height = match.group(6).strip()

        # Map lattice rail height to full dimension
        # Based on template: 1.5x5.5" and 2x3.5"
        normalized_height = normalize_value(lattice_rail_height)
        if '5' in normalized_height and '5' == normalized_height.replace('-', ''):
            lattice_rail = '1-5-x-5-5'
        elif normalized_height in ['3-5', '35']:
            lattice_rail = '2-x-3-5'
        else:
            # Try direct mapping based on the raw value
            if '5.5' in lattice_rail_height or '5-5' in lattice_rail_height:
                lattice_rail = '1-5-x-5-5'
            elif '3.5' in lattice_rail_height or '3-5' in lattice_rail_height:
                lattice_rail = '2-x-3-5'
            else:
                lattice_rail = normalized_height

        return {
            'category_id': CATEGORY_MAP['vinyl-privacy-with-lattice-top'],
            'body_height': body,
            'picket_size': picket,
            'rail_size': rail,
            'lattice_top_rail_size': lattice_rail,
            'panel_width': width,
            'filename': filename
        }
    return None

def parse_simple_rail_filename(filename):
    """
    Parse simple rail patterns: 2-Rail-Vinyl-Fence.pdf, 3-Rail-Vinyl-Fence.pdf, 4-Rail-Vinyl-Fence.pdf
    """
    if '2-Rail-Vinyl-Fence' in filename:
        return {
            'category_id': CATEGORY_MAP['vinyl-2-rail'],
            'filename': filename
        }
    elif '3-Rail-Vinyl-Fence' in filename:
        return {
            'category_id': CATEGORY_MAP['vinyl-3-rail'],
            'filename': filename
        }
    elif '4-Rail-Vinyl-Fence' in filename:
        return {
            'category_id': CATEGORY_MAP['vinyl-4-rail'],
            'filename': filename
        }
    return None

def classify_pdf(filename):
    """
    Classify PDF into appropriate category and extract filter values
    """
    # Check for lattice first (more specific pattern)
    if 'Lattice' in filename:
        result = parse_lattice_filename(filename)
        if result:
            return result

    # Check for simple rail patterns
    result = parse_simple_rail_filename(filename)
    if result:
        return result

    # Check for privacy pattern (general picket pattern)
    result = parse_privacy_filename(filename)
    if result:
        return result

    # If no pattern matched, return None
    return None

def generate_insert_statement(data):
    """Generate SQL INSERT statement based on category"""
    category_id = data['category_id']
    filename = data['filename']
    pdf_url = f"https://staging2.wholesalefencing.com/wp-content/uploads/profiles/{filename}"

    # For simple rail categories (2-rail, 3-rail, 4-rail)
    if category_id in [CATEGORY_MAP['vinyl-2-rail'], CATEGORY_MAP['vinyl-3-rail'], CATEGORY_MAP['vinyl-4-rail']]:
        return f"INSERT INTO wpg0_profiles (category_id, pdf_url, created_at) VALUES ({category_id}, '{pdf_url}', NOW());"

    # For vinyl-privacy
    elif category_id == CATEGORY_MAP['vinyl-privacy']:
        return f"INSERT INTO wpg0_profiles (category_id, body_height, picket_size, rail_size, panel_width, pdf_url, created_at) VALUES ({category_id}, '{data['body_height']}', '{data['picket_size']}', '{data['rail_size']}', '{data['panel_width']}', '{pdf_url}', NOW());"

    # For vinyl-privacy-with-lattice-top
    elif category_id == CATEGORY_MAP['vinyl-privacy-with-lattice-top']:
        return f"INSERT INTO wpg0_profiles (category_id, body_height, picket_size, rail_size, lattice_top_rail_size, panel_width, pdf_url, created_at) VALUES ({category_id}, '{data['body_height']}', '{data['picket_size']}', '{data['rail_size']}', '{data['lattice_top_rail_size']}', '{data['panel_width']}', '{pdf_url}', NOW());"

    return None

def main():
    profiles_dir = r'c:\Users\user\Downloads\CSVUpdationScripts\profiles'

    # Get all PDF files
    pdf_files = [f for f in os.listdir(profiles_dir) if f.endswith('.pdf')]

    print(f"Total PDF files found: {len(pdf_files)}\n")

    # Statistics
    stats = {cat: 0 for cat in CATEGORY_MAP.keys()}
    unclassified = []

    # SQL statements by category
    sql_by_category = {cat: [] for cat in CATEGORY_MAP.keys()}

    # Process each PDF
    for pdf_file in pdf_files:
        result = classify_pdf(pdf_file)

        if result:
            # Find category name
            category_name = None
            for cat_name, cat_id in CATEGORY_MAP.items():
                if cat_id == result['category_id']:
                    category_name = cat_name
                    break

            if category_name:
                stats[category_name] += 1
                sql_stmt = generate_insert_statement(result)
                if sql_stmt:
                    sql_by_category[category_name].append(sql_stmt)
        else:
            unclassified.append(pdf_file)

    # Print statistics
    print("=== CLASSIFICATION STATISTICS ===")
    for cat_name, count in stats.items():
        print(f"{cat_name}: {count} files")
    print(f"Unclassified: {len(unclassified)} files")
    print()

    # Write SQL file
    output_file = r'c:\Users\user\Downloads\CSVUpdationScripts\insert-all-profiles-staging.sql'
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write("-- Profile inserts for all categories (Staging Site)\n")
        f.write("-- Generated automatically from PDF filenames\n\n")

        for cat_name, cat_id in CATEGORY_MAP.items():
            if sql_by_category[cat_name]:
                f.write(f"\n-- {cat_name.upper()} (Category ID: {cat_id})\n")
                f.write(f"-- Total: {len(sql_by_category[cat_name])} profiles\n")
                for sql in sql_by_category[cat_name]:
                    f.write(sql + '\n')

    print(f"SQL file generated: {output_file}")
    print(f"Total SQL statements: {sum(len(sqls) for sqls in sql_by_category.values())}")

    # Write unclassified files
    if unclassified:
        unclassified_file = r'c:\Users\user\Downloads\CSVUpdationScripts\unclassified-profiles.txt'
        with open(unclassified_file, 'w', encoding='utf-8') as f:
            f.write("Unclassified PDF files:\n\n")
            for pdf in unclassified:
                f.write(pdf + '\n')
        print(f"\nUnclassified files written to: {unclassified_file}")

if __name__ == '__main__':
    main()
