import pandas as pd
import os

file_path = 'data.xlsx'
if os.path.exists(file_path):
    xl = pd.ExcelFile(file_path)
    buildings = set()
    for sheet_name in xl.sheet_names:
        df = pd.read_excel(file_path, sheet_name=sheet_name)
        # Assuming building names are in a column named 'Building' or 'Building Name' or similar
        # Since I don't know the exact column name, I'll search for it or just dump all unique values from columns that look like they contain building names.
        for col in df.columns:
            if 'building' in str(col).lower():
                buildings.update(df[col].dropna().unique())
    
    print("Buildings found in Excel:")
    for b in sorted(list(buildings)):
        print(f"- {b}")
else:
    print("data.xlsx not found")
