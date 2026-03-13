import csv
import anthropic
import os
from pathlib import Path

# Anthropic API client setup
# Get API key from environment or use default
api_key = os.environ.get("ANTHROPIC_API_KEY")
if not api_key:
    # Try to read from .env file or ask user
    print("WARNING: ANTHROPIC_API_KEY not found in environment variables")
    print("Please set it using: set ANTHROPIC_API_KEY=your_key_here")
    # For now, using a placeholder - will fail if not set
    api_key = "sk-ant-api03-placeholder"

client = anthropic.Anthropic(api_key=api_key)

def generate_short_description(product_name, long_description, current_short_desc):
    """
    Generate a 3-4 line short description based on the product's long description.
    """
    prompt = f"""Based on this product information, create a short 3-4 line description that is engaging and highlights the key features:

Product Name: {product_name}
Long Description: {long_description}

Requirements:
- 3-4 lines maximum
- Focus on key features and benefits
- Professional tone
- Easy to read
- Don't repeat the product code or SKU

Generate ONLY the short description text, nothing else."""

    try:
        message = client.messages.create(
            model="claude-3-5-sonnet-20241022",
            max_tokens=200,
            messages=[{
                "role": "user",
                "content": prompt
            }]
        )

        generated_text = message.content[0].text.strip()

        # Combine current short description with generated text
        final_short_desc = f"{current_short_desc}\n\n{generated_text}"

        return final_short_desc

    except Exception as e:
        print(f"Error generating description: {e}")
        return current_short_desc  # Return original if error

def process_csv(input_file, output_file):
    """
    Process CSV file and generate short descriptions for all products.
    """
    print(f"Reading CSV file: {input_file}")

    with open(input_file, 'r', encoding='utf-8', newline='') as infile:
        reader = csv.DictReader(infile)
        fieldnames = reader.fieldnames

        rows = list(reader)
        total_products = len(rows)

        print(f"Found {total_products} products to process")

        # Process each row
        for idx, row in enumerate(rows, 1):
            product_name = row['Name']
            long_description = row['Description']
            current_short_desc = row['Short description']

            print(f"\n[{idx}/{total_products}] Processing: {product_name[:50]}...")

            # Skip if no description or already has multi-line short description
            if not long_description or '\n\n' in current_short_desc:
                print(f"  [SKIP] Skipping (no description or already processed)")
                continue

            # Generate new short description
            new_short_desc = generate_short_description(
                product_name,
                long_description,
                current_short_desc
            )

            # Update row
            row['Short description'] = new_short_desc

            print(f"  [OK] Generated short description")

    # Write updated CSV
    print(f"\nWriting updated CSV to: {output_file}")

    with open(output_file, 'w', encoding='utf-8', newline='') as outfile:
        writer = csv.DictWriter(outfile, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(rows)

    print(f"\n[OK] Successfully processed {total_products} products!")
    print(f"[OK] Output saved to: {output_file}")

if __name__ == "__main__":
    input_csv = r"C:\Users\user\Downloads\FencingsProductData 05-03-2026 - FencingsProductData 05-03-2026.csv"
    output_csv = r"C:\Users\user\Downloads\FencingsProductData_Updated_ShortDescriptions.csv"

    print("=" * 70)
    print("Product Short Description Generator")
    print("=" * 70)

    process_csv(input_csv, output_csv)

    print("\n" + "=" * 70)
    print("Process Complete!")
    print("=" * 70)
