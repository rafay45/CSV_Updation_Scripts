import csv
import re
from pathlib import Path

def extract_key_features(long_description):
    """
    Extract key features from the long description without using AI.
    Creates a 3-4 line summary based on text analysis.
    """
    if not long_description:
        return ""

    # Split into sentences
    sentences = re.split(r'[.!?]+', long_description)
    sentences = [s.strip() for s in sentences if s.strip() and len(s.strip()) > 20]

    if not sentences:
        return ""

    # Take first 3-4 most informative sentences
    # Priority: sentences with keywords like "designed", "ideal", "features", "provides"
    priority_keywords = ['designed', 'ideal', 'features', 'provides', 'offers', 'perfect', 'suitable']

    scored_sentences = []
    for sentence in sentences[:8]:  # Only check first 8 sentences
        score = 0
        sentence_lower = sentence.lower()

        # Score based on keywords
        for keyword in priority_keywords:
            if keyword in sentence_lower:
                score += 2

        # Prefer sentences with measurements or specifications
        if re.search(r'\d+', sentence):
            score += 1

        # Prefer sentences about applications or benefits
        if any(word in sentence_lower for word in ['residential', 'commercial', 'contractors', 'installation']):
            score += 1

        scored_sentences.append((score, sentence))

    # Sort by score and take top sentences
    scored_sentences.sort(reverse=True, key=lambda x: x[0])

    # Take 2-3 best sentences
    selected = [s[1] for s in scored_sentences[:3]]

    # If we have good sentences, use them
    if selected:
        summary = ' '.join(selected)

        # Trim if too long (max ~200 chars per line, 3 lines = 600 chars)
        if len(summary) > 600:
            summary = summary[:597] + '...'

        return summary

    # Fallback: take first 3 sentences
    return ' '.join(sentences[:3])

def process_csv(input_file, output_file):
    """
    Process CSV file and generate short descriptions for all products.
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

        # Process each row
        for idx, row in enumerate(rows, 1):
            product_name = row['Name']
            long_description = row['Description']
            current_short_desc = row['Short description']

            if idx % 100 == 0:
                print(f"Progress: {idx}/{total_products} products...")

            # Skip if no description or already has multi-line short description
            if not long_description:
                skipped_count += 1
                continue

            if '<br><br>' in current_short_desc or '\n\n' in current_short_desc or '<p>' in current_short_desc or '<h4>' in current_short_desc:
                skipped_count += 1
                continue

            # Generate summary from description
            summary = extract_key_features(long_description)

            if summary:
                # Combine current short description with generated summary
                # Use <h4> for product code and <p> for description
                new_short_desc = f"<h4>{current_short_desc}</h4>\n<p>{summary}</p>"
                row['Short description'] = new_short_desc
                processed_count += 1
            else:
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
    print(f"\nOutput saved to: {output_file}")
    print(f"{'='*70}")

if __name__ == "__main__":
    input_csv = r"C:\Users\user\Downloads\FencingsProductData 05-03-2026 - FencingsProductData 05-03-2026.csv"
    output_csv = r"C:\Users\user\Downloads\FencingsProductData_Updated_ShortDescriptions.csv"

    print("="*70)
    print("Product Short Description Generator (Local Version)")
    print("="*70)
    print()

    process_csv(input_csv, output_csv)

    print("\nProcess Complete!")
    print("="*70)
