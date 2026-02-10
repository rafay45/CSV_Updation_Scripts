import pandas as pd
master = pd.read_csv('Master File - Web Clean - Export_Clean.csv', header=4, encoding='utf-8', low_memory=False)

print('All columns in Master File:')
for i, col in enumerate(master.columns):
    print(f'{i:3d}: {col}')
