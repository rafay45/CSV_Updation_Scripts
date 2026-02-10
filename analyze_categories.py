import pandas as pd
import sys

path = 'CSVWholesalefencing - MappingCSVfileAccordingToWordpress_WithCategories (1).csv'
try:
    df = pd.read_csv(path, dtype=str)
except Exception as e:
    print('ERROR', e)
    sys.exit(1)

cats = df.get('Categories', pd.Series(['']*len(df))).fillna('').astype(str).str.strip()
main = cats.str.split(' > ').str[0].fillna('').astype(str).str.strip()
sub = cats.str.split(' > ').str[1].fillna('').astype(str).str.strip()

total = len(df)
print(f'Total rows: {total}\n')

main_counts = main.value_counts()
print('Main categories and counts:')
for m, c in main_counts.items():
    print(f'  {m}: {c}')

print('\nSub-categories per main category:')
for m in main_counts.index:
    subs = sub[main == m]
    subs = subs[subs.str_len() > 0] if hasattr(subs, 'str_len') else subs[subs != '']
    # pandas older versions may not have str.len; use condition
    subs = subs[subs != '']
    unique_subs = sorted(set(subs.tolist()))
    print(f'  {m}: {len(unique_subs)} sub-categories')
    if unique_subs:
        print('    ' + ', '.join(unique_subs[:50]))

# Print rows with empty category
empty_count = (cats == '').sum()
print(f'\nEmpty/uncategorized rows: {empty_count}')
