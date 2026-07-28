import pandas as pd
import os

file_path = 'data.xlsx'
if os.path.exists(file_path):
    xl = pd.ExcelFile(file_path)
    for sheet_name in xl.sheet_names:
        df = pd.read_excel(file_path, sheet_name=sheet_name, header=None)
        for r in range(len(df)):
            for c in range(len(df.columns)):
                val = str(df.iloc[r, c]).lower()
                if "leyshak" in val or "leshak" in val:
                    print(f"[{sheet_name}] Row {r}, Col {c}: '{df.iloc[r, c]}'")
else:
    print("data.xlsx not found")
