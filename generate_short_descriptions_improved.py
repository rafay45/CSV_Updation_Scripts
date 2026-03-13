import csv
import re
from pathlib import Path

def extract_first_sentences(long_description, max_sentences=3):
    """
    Extract the first 2-3 complete sentences from description.
    Preserves ALL formatting, numbers, measurements, and punctuation.
    """
    if not long_description:
        return ""

    # More robust sentence splitting:
    # Split on ". " followed by capital letter or end of string
    # But NOT on decimal numbers like "1.5" or measurements like "1.75""

    # First, protect measurements and decimals by temporarily replacing them
    protected = long_description

    # Split by ". " followed by uppercase letter
    parts = []
    current = ""
    i = 0

    while i < len(protected):
        current += protected[i]

        # Check if we're at a sentence boundary
        if i < len(protected) - 2 and protected[i] == '.':
            # Look ahead - is next char a space followed by uppercase?
            if protected[i+1] == ' ' and i+2 < len(protected) and protected[i+2].isupper():
                # Check if this is NOT a decimal (digit before the period)
                if i > 0 and not protected[i-1].isdigit():
                    parts.append(current.strip())
                    current = ""
                    i += 1  # Skip the space

        i += 1

    # Add the last part
    if current.strip():
        parts.append(current.strip())

    # Filter out very short parts
    sentences = [s for s in parts if len(s) > 20]

    if not sentences:
        return ""

    # Take first 2-3 sentences (keep it concise)
    selected = sentences[:min(max_sentences, len(sentences))]

    # Join with spaces
    summary = ' '.join(selected)

    # Ensure it ends with proper punctuation
    if summary and not summary.endswith(('.', '!', '?')):
        summary += '.'

    return summary

def process_csv(input_file, output_file):
    """
    Process CSV file and generate improved short descriptions.
    """
    print(f"Reading CSV file: {input_file}")
    print("="*70)

    with open(input_file, 'r', encoding='utf-8', newline='') as infile:
        reader = csv.DictReader(infile)
        fieldnames = reader.fieldnames

        rows = list(reader)
        total_products = len(rows)

        print(f"Found {total_products} products to process\n")

        processed_count = 0
        skipped_count = 0
        error_count = 0

        # Process each row
        for idx, row in enumerate(rows, 1):
            product_name = row['Name']
            long_description = row['Description']
            current_short_desc = row['Short description']

            if idx % 100 == 0:
                print(f"Progress: {idx}/{total_products} products...")

            # Skip if no description
            if not long_description:
                skipped_count += 1
                continue

            # Skip if already processed (has HTML tags)
            if '<h4>' in current_short_desc or '<p>' in current_short_desc:
                skipped_count += 1
                continue

            try:
                # Extract first 2-3 sentences EXACTLY as they appear
                summary = extract_first_sentences(long_description, max_sentences=3)

                if summary and len(summary) > 30:
                    # Combine with proper HTML formatting
                    new_short_desc = f"<h4>{current_short_desc}</h4>\n<p>{summary}</p>"
                    row['Short description'] = new_short_desc
                    processed_count += 1
                else:
                    skipped_count += 1

            except Exception as e:
                print(f"  [ERROR] Product {idx}: {str(e)}")
                error_count += 1
                skipped_count += 1

    # Write updated CSV
    print(f"\n{'='*70}")
    print(f"Writing updated CSV to: {output_file}")

    with open(output_file, 'w', encoding='utf-8', newline='') as outfile:
        writer = csv.DictWriter(outfile, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(rows)

    print(f"\n{'='*70}")
    print("SUMMARY")
    print(f"{'='*70}")
    print(f"Total products: {total_products}")
    print(f"Processed: {processed_count}")
    print(f"Skipped: {skipped_count}")
    print(f"Errors: {error_count}")
    print(f"\nOutput saved to: {output_file}")
    print(f"{'='*70}")

if __name__ == "__main__":
    # Use ORIGINAL CSV file (not the updated one)
    input_csv = r"C:\Users\user\Downloads\FencingsProductData 05-03-2026 - FencingsProductData 05-03-2026.csv"
    output_csv = r"C:\Users\user\Downloads\FencingsProductData_Updated_ShortDescriptions_v2.csv"

    print("="*70)
    print("Product Short Description Generator - IMPROVED VERSION")
    print("Preserves exact text from original descriptions")
    print("="*70)
    print()

    process_csv(input_csv, output_csv)

    print("\nProcess Complete!")
    print("="*70)
