import pandas as pd
import os

file_path = 'data.xlsx'
if os.path.exists(file_path):
    xl = pd.ExcelFile(file_path)
    for sheet_name in xl.sheet_names:
        df = pd.read_excel(file_path, sheet_name=sheet_name, header=None)
        all_text = []
        for r in range(len(df)):
            for c in range(len(df.columns)):
                val = str(df.iloc[r, c])
                if any(k in val.lower() for k in ["sunney", "hostel", "health", "near health", "dept"]):
                    print(f"[{sheet_name}] Row {r}, Col {c}: '{val}'")
else:
    print("data.xlsx not found")
