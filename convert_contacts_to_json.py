import pandas as pd
import json
import os

def convert_excel_to_json():
    excel_file = '社內分機表/1. 讀書共和國社內分機表(250715).xlsx'
    try:
        print(f"正在讀取 Excel 檔案: {excel_file}")
        df = pd.read_excel(excel_file, header=None)
        print(f"Excel 檔案總行數: {len(df)}，總欄數: {len(df.columns)}")
        contacts = []
        # 以每三行為一組，橫向展開
        for row_base in range(0, len(df), 3):
            if row_base + 2 >= len(df):
                break
            dept_row = df.iloc[row_base]
            ext_row = df.iloc[row_base + 1]
            name_row = df.iloc[row_base + 2]
            for col in range(len(df.columns)):
                department = str(dept_row[col]).strip() if pd.notna(dept_row[col]) else ''
                extension = str(ext_row[col]).strip() if pd.notna(ext_row[col]) else ''
                name = str(name_row[col]).strip() if pd.notna(name_row[col]) else ''
                # 過濾空值與無效資料
                if name and extension and department and extension != 'nan' and name != 'nan' and department != 'nan':
                    # 清理分機號碼（移除 .0）
                    if extension.endswith('.0'):
                        extension = extension[:-2]
                    contacts.append({
                        'name': name,
                        'extension': extension,
                        'department': department
                    })
        print(f"共抓到 {len(contacts)} 筆聯絡資訊")
        # 儲存 JSON
        json_file = 'assets/js/contacts_data.json'
        os.makedirs(os.path.dirname(json_file), exist_ok=True)
        with open(json_file, 'w', encoding='utf-8') as f:
            json.dump(contacts, f, ensure_ascii=False, indent=2)
        print(f"JSON 檔案已儲存至: {json_file}")
        print("前 10 筆資料範例:")
        for i, c in enumerate(contacts[:10]):
            print(f"{i+1}. {c}")
    except Exception as e:
        print(f"錯誤: {e}")
        import traceback
        traceback.print_exc()

if __name__ == "__main__":
    convert_excel_to_json() 