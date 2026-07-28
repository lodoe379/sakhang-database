import pandas as pd
import os

file_path = 'data.xlsx'
if os.path.exists(file_path):
    xl = pd.ExcelFile(file_path)
    for sheet_name in xl.sheet_names:
        df = pd.read_excel(file_path, sheet_name=sheet_name, header=None)
        # Check specific locations known from previous logs
        # Row 31, Col 20 (Health)
        # Row 101, Col 30 (Sunney)
        try:
            val_31_20 = df.iloc[31, 20]
            print(f"Sheet '{sheet_name}' Row 31, Col 20: '{val_31_20}'")
        except: pass
        
        try:
            val_101_30 = df.iloc[101, 30]
            print(f"Sheet '{sheet_name}' Row 101, Col 30: '{val_101_30}'")
        except: pass

        # Also search for the old names
        for r in range(len(df)):
            for c in range(len(df.columns)):
                cell = str(df.iloc[r, c]).lower()
                if "health" in cell or "sunney" in cell or "hostel" in cell:
                    print(f"Found '{df.iloc[r, c]}' at Row {r}, Col {c} in sheet '{sheet_name}'")
else:
    print("data.xlsx not found")
