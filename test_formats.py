import csv

# Test different HTML break formats
input_file = r"C:\Users\user\Downloads\FencingsProductData 05-03-2026 - FencingsProductData 05-03-2026.csv"

# Read first product
with open(input_file, 'r', encoding='utf-8') as f:
    reader = csv.DictReader(f)
    row = next(reader)

    current_short = row['Short description']
    desc = row['Description']

    # Extract first 2-3 sentences
    sentences = desc.split('. ')[:3]
    summary = '. '.join(sentences) + '.'

    print("Current Short Description:")
    print(current_short)
    print("\n" + "="*70 + "\n")

    print("FORMAT 1 - Double <br> tags:")
    format1 = f"{current_short}<br><br>{summary}"
    print(format1)
    print("\n" + "="*70 + "\n")

    print("FORMAT 2 - <br /> with space:")
    format2 = f"{current_short}<br /><br />{summary}"
    print(format2)
    print("\n" + "="*70 + "\n")

    print("FORMAT 3 - Paragraph tags:")
    format3 = f"<p>{current_short}</p><p>{summary}</p>"
    print(format3)
    print("\n" + "="*70 + "\n")

    print("FORMAT 4 - \\n\\n (plain newlines):")
    format4 = f"{current_short}\n\n{summary}"
    print(format4)
    print("\n" + "="*70 + "\n")

    print("\nRECOMMENDATION:")
    print("Try FORMAT 3 (paragraph tags) - most reliable in WordPress")
