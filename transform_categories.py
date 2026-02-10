#!/usr/bin/env python3
import pandas as pd
import numpy as np
import os

# Read both files
print("Reading Master File...")
      # Master File has header at row 4 (skip 4 rows)
master_df = pd.read_csv('Master File - Web Clean - Export_Clean.csv', header=4, encoding='utf-8')

print("Reading Wholesale CSV...")
wholesale_df = pd.read_csv('WholesalefencingCSV - CSVWholesalefencing - MappingCSVfileAccordingToWordpress_WithCategories (1) (1).csv', encoding='utf-8')

print(f"\nMaster File shape: {master_df.shape}")
print(f"Wholesale CSV shape: {wholesale_df.shape}")

# Display column names from both files to verify structure
print("\nMaster File columns (first 50):")
print(list(master_df.columns)[:50])

print("\nWholesale CSV columns (first 20):")
print(list(wholesale_df.columns)[:20])

# Find the category columns in Master File
category_cols = ['VINYL', 'CHAIN LINK', 'ORNAMENTAL', 'MODERN', 'SIMTEK', 'TREX', 'AGRICULTURE', 'METAL HORSE FENCE']
subcategory_cols = ['Vinyl Subcategory', 'Ornamental Subcategory', 'Modern Subcategory', 'Simtek Subcategory', 
                    'Trex Subcategory', 'Agriculture Subcategory']

print("\n\nChecking available category columns in Master File:")
for col in category_cols:
    if col in master_df.columns:
        print(f"  ✓ {col}")
    else:
        print(f"  ✗ {col}")

print("\nChecking available subcategory columns in Master File:")
for col in subcategory_cols:
    if col in master_df.columns:
        print(f"  ✓ {col}")
    else:
        print(f"  ✗ {col}")

# Check SKU column names
print(f"\nMaster File SKU column name: {master_df.columns[3]}")
print(f"Wholesale CSV SKU column name: {wholesale_df.columns[1]}")
