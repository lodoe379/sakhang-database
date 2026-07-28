import pandas as pd
import os

file_path = 'data.xlsx'
if os.path.exists(file_path):
    xl = pd.ExcelFile(file_path)
    for sheet_name in xl.sheet_names:
        print(f"\nSheet: {sheet_name}")
        df = pd.read_excel(file_path, sheet_name=sheet_name, nrows=5)
        print(df.columns.tolist())
        print(df.head())
else:
    print("data.xlsx not found")
