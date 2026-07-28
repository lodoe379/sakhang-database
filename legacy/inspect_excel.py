import pandas as pd
import os

file_path = 'data.xlsx'
if os.path.exists(file_path):
    xl = pd.ExcelFile(file_path)
    for sheet_name in xl.sheet_names:
        print(f"\nSheet: {sheet_name}")
        df = pd.read_excel(file_path, sheet_name=sheet_name, header=None)
        # Scan first few rows to find headers
        for r in range(min(20, len(df))):
            row_vals = [str(x).lower() for x in df.iloc[r] if not pd.isna(x)]
            if any('meter' in v for v in row_vals):
                print(f"Row {r} headers: {row_vals}")
                break
else:
    print("data.xlsx not found")
