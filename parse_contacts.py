import csv
import json
import re

def is_extension(value):
    return bool(value) and re.match(r'^\d{3,4}$', str(value))

def is_chinese_name(value):
    return bool(value) and re.match(r'^[\u4e00-\u9fff]{2,8}$', str(value))

def parse_contacts_csv_vertical(csv_file):
    results = []
    last_dept = None
    with open(csv_file, encoding='utf-8-sig') as f:
        reader = csv.reader(f)
        for row in reader:
            dept = row[0].strip() if row and row[0] else last_dept
            ext = row[1].strip() if len(row) > 1 and row[1] else ''
            name = row[2].strip() if len(row) > 2 and row[2] else ''
            if dept:
                last_dept = dept
            if dept and is_extension(ext) and is_chinese_name(name):
                results.append({
                    '部門': dept,
                    '分機': ext,
                    '姓名': name
                })
    return results

def main():
    csv_file = '社內分機表/1. 讀書共和國社內分機表(250715).csv'
    contacts = parse_contacts_csv_vertical(csv_file)
    print(f"共抓到 {len(contacts)} 筆資料")
    with open("parsed_contacts.json", "w", encoding="utf-8") as f:
        json.dump(contacts, f, ensure_ascii=False, indent=2)
    for c in contacts[:10]:
        print(c)

if __name__ == "__main__":
    main() 