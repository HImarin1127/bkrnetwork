import csv
import os

def clean_string(s):
    if not isinstance(s, str):
        s = str(s)
    return s.replace("'", "''")

def process_seats_file(filename):
    try:
        print(f"Processing seats file: {filename}")
        # 生成座位 SQL
        with open('seats_data.sql', 'w', encoding='utf-8') as f:
            f.write("-- 清空現有資料\n")
            f.write("TRUNCATE TABLE employee_seats;\n\n")
            f.write("-- 插入座位資料\n")
            
            # 讀取 CSV 檔案
            with open(filename, 'r', encoding='utf-8') as csvfile:
                reader = csv.DictReader(csvfile)
                for row in reader:
                    try:
                        sql = f"INSERT INTO employee_seats (employee_name, floor_number, seat_number, department_id, extension_number) VALUES ('{clean_string(row['姓名'])}', {row['樓層']}, '{clean_string(row['座位號'])}', {row['部門ID']}, '{clean_string(row['分機號碼'])}');\n"
                        f.write(sql)
                    except Exception as e:
                        print(f"Error processing row in seats file: {e}")
                        print(f"Row data: {row}")
                print("座位資料已轉換為 SQL")
    except Exception as e:
        print(f"Error processing seats file: {e}")

def process_extensions_file(filename):
    try:
        print(f"Processing extensions file: {filename}")
        # 生成分機 SQL
        with open('extensions_data.sql', 'w', encoding='utf-8') as f:
            f.write("-- 清空現有資料\n")
            f.write("TRUNCATE TABLE extension_numbers;\n\n")
            f.write("-- 插入分機資料\n")
            
            # 讀取 CSV 檔案
            with open(filename, 'r', encoding='utf-8') as csvfile:
                reader = csv.DictReader(csvfile)
                for row in reader:
                    try:
                        sql = f"INSERT INTO extension_numbers (extension_number, employee_name, department_id, description) VALUES ('{clean_string(row['分機號碼'])}', '{clean_string(row['姓名'])}', {row['部門ID']}, '{clean_string(row.get('說明', ''))}');\n"
                        f.write(sql)
                    except Exception as e:
                        print(f"Error processing row in extensions file: {e}")
                        print(f"Row data: {row}")
                print("分機資料已轉換為 SQL")
    except Exception as e:
        print(f"Error processing extensions file: {e}")

if __name__ == "__main__":
    # 讀取座位圖
    seat_file = '座位表.csv'  # 請將 Excel 檔案另存為 CSV
    if os.path.exists(seat_file):
        process_seats_file(seat_file)
    else:
        print(f"請將 '⊙簡版平面座位圖(250401).xlsx' 另存為 '{seat_file}'")

    # 讀取分機表
    ext_file = '分機表.csv'  # 請將 Excel 檔案另存為 CSV
    if os.path.exists(ext_file):
        process_extensions_file(ext_file)
    else:
        print(f"請將 '1. 讀書共和國社內分機表(241226).xlsx' 另存為 '{ext_file}'") 