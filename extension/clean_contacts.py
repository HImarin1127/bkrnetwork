import pandas as pd
import numpy as np
import re

# 讀取 CSV 檔案（無標題列）
df = pd.read_csv('社內分機表/1. 讀書共和國社內分機表(250715).csv', header=None)

# 只抓前15欄，排除右側外部單位
max_internal_cols = 18
# 跳過前兩列，取得實際資料
# 只取前18欄
if df.shape[1] > max_internal_cols:
    df_data = df.iloc[2:, :max_internal_cols].copy()
else:
    df_data = df.iloc[2:].copy()

col_names = ['部門/職稱', '分機號碼', '姓名']
all_dfs = []

# 每三欄為一組，分別為部門/職稱、分機號碼、姓名
for i in range(0, df_data.shape[1], 3):
    block = df_data.iloc[:, i:i+3]
    if block.shape[1] == 3:
        block.columns = col_names
        all_dfs.append(block)

# 合併所有 block
final_df = pd.concat(all_dfs, ignore_index=True)

# 清理資料
final_df.replace('nan', np.nan, inplace=True)
final_df['分機號碼'] = pd.to_numeric(final_df['分機號碼'], errors='coerce')
final_df['部門/職稱'] = final_df['部門/職稱'].fillna(method='ffill')
final_df['分機號碼'] = final_df['分機號碼'].fillna(method='ffill')
final_df.dropna(subset=['分機號碼'], inplace=True)
final_df.dropna(subset=['姓名'], inplace=True)
for col in ['部門/職稱', '姓名']:
    if col in final_df.columns:
        final_df[col] = final_df[col].astype(str).str.strip()
final_df['分機號碼'] = final_df['分機號碼'].astype(int)
final_df.reset_index(drop=True, inplace=True)

# 展開每一列的姓名（如果有多行）
rows = []
for _, row in final_df.iterrows():
    names = str(row['姓名']).strip().split('\n')
    for name in names:
        name = name.strip()
        if name:
            rows.append({
                '部門/職稱': row['部門/職稱'],
                '分機號碼': int(row['分機號碼']),
                '姓名': name
            })

cleaned_df = pd.DataFrame(rows)
cleaned_df.reset_index(drop=True, inplace=True)
cleaned_df.to_json('社內分機表/cleaned_contacts.json', orient='records', force_ascii=False, indent=2)

# 印出前10筆資料與 DataFrame 資訊
print("清理後的資料前10列:")
print(cleaned_df.head(10))
print("\n清理後的資料資訊:")
print(cleaned_df.info()) 