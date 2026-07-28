import pandas as pd
df = pd.read_excel('data.xlsx', sheet_name='Sheet1', header=None)
print(df.iloc[0:10, 40:50])
