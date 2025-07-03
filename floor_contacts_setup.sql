-- 建立樓層資訊表
CREATE TABLE IF NOT EXISTS floor_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    floor_number INT NOT NULL,
    floor_name VARCHAR(100) NOT NULL,
    floor_description TEXT,
    floor_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 建立部門聯絡資訊表
CREATE TABLE IF NOT EXISTS department_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL,
    floor_number INT NOT NULL,
    extension_range VARCHAR(50),
    email VARCHAR(100),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 建立員工座位表
CREATE TABLE IF NOT EXISTS employee_seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_name VARCHAR(50) NOT NULL,
    floor_number INT NOT NULL,
    seat_number VARCHAR(20) NOT NULL,
    department_id INT,
    extension_number VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 建立分機號碼表
CREATE TABLE IF NOT EXISTS extension_numbers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    extension_number VARCHAR(20) NOT NULL,
    employee_name VARCHAR(50) NOT NULL,
    department_id INT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 插入基本樓層資訊
INSERT INTO floor_info (floor_number, floor_name, floor_description, floor_type) VALUES
(7, '總經理辦公室', '分機: 101\n信箱: ceo@bookrep.com.tw', '行政區'),
(6, '編輯部', '分機: 201-210\n信箱: editorial@bookrep.com.tw', '出版區'),
(5, '業務部', '分機: 301-315\n信箱: sales@bookrep.com.tw', '營業區'),
(4, '製作部', '分機: 401-410\n信箱: production@bookrep.com.tw', '出版區'),
(5, '行銷部', '分機: 316-325\n信箱: marketing@bookrep.com.tw', '營業區'),
(3, '人事行政部', '分機: 501-505\n信箱: hr@bookrep.com.tw', '行政區'),
(3, '財務會計部', '分機: 601-608\n信箱: finance@bookrep.com.tw', '行政區'),
(3, '資訊部', '分機: 701-705\n信箱: it@bookrep.com.tw', '行政區');

-- 插入基本部門聯絡資訊
INSERT INTO department_contacts (department_name, floor_number, extension_range, email) VALUES
('總經理辦公室', 7, '101', 'ceo@bookrep.com.tw'),
('編輯部', 6, '201-210', 'editorial@bookrep.com.tw'),
('業務部', 5, '301-315', 'sales@bookrep.com.tw'),
('行銷部', 5, '316-325', 'marketing@bookrep.com.tw'),
('製作部', 4, '401-410', 'production@bookrep.com.tw'),
('人事行政部', 3, '501-505', 'hr@bookrep.com.tw'),
('財務會計部', 3, '601-608', 'finance@bookrep.com.tw'),
('資訊部', 3, '701-705', 'it@bookrep.com.tw');

-- 清空現有資料
TRUNCATE TABLE employee_seats;
TRUNCATE TABLE extension_numbers;

-- 設置部門 ID 對照
-- 1: 總經理室
-- 2: 編輯部
-- 3: 業務部
-- 4: 行銷部
-- 5: 製作部
-- 6: 人事部
-- 7: 會計部
-- 8: 資訊部

-- 108-3號3F (出版單位)
INSERT INTO employee_seats (employee_name, floor_number, seat_number, department_id, extension_number) VALUES
('陳建中', 3, '1290', 2, '3290'),
('賴俊傑', 3, '1206', 2, '1206'),
('陳怡安', 3, '1236', 2, '1236'),
('胡佳芬', 3, '3325', 2, '3325'),
('吳佐晉', 3, '1213', 2, '1213'),
('翁文創', 3, '1212', 2, '1212'),
('蔡沛青', 3, '1217', 2, '1217'),
('李瓊瑩', 3, '1211', 2, '1211'),
('李璟玲', 3, '1215', 2, '1215'),
('丁維瑄', 3, '1219', 2, '1219'),
('塗惠美', 3, '1233', 2, '1233'),
('吳玉珠', 3, '3221', 2, '3221'),
('邱子豪', 3, '1216', 2, '1216'),
('宋俊德', 3, '1214', 2, '1214'),
('張德善', 3, '3315', 2, '3315'),
('陳怡秀', 3, '3202', 2, '3202'),
('宋慧昀', 3, '3415', 2, '3415'),
('王義宇', 3, '1234', 2, '1234'),
('蔣玉', 3, '1221', 2, '1221'),
('賴昭君', 3, '3222', 2, '3222'),
('洪偉倫', 3, '3218', 2, '3218'),
('賴慧玉', 3, '3220', 2, '3220'),
('潘冠云', 3, '3391', 2, '3391'),
('柯瑞云', 3, '3166', 2, '3166'),
('洪德海', 3, '3389', 2, '3389'),
('曹美君', 3, '3388', 2, '3388'),
('董昌明', 3, '3390', 2, '3390'),
('蕭詩晴', 3, '3206', 2, '3206'),
('張詩晴', 3, '3151', 2, '3151'),
('邱昌豪', 3, '1223', 2, '1223'),
('趙淑怡', 3, '1220', 2, '1220');

-- 108-4號5F (出版單位 + 營業單位)
INSERT INTO employee_seats (employee_name, floor_number, seat_number, department_id, extension_number) VALUES
('李建華', 5, '1117', 3, '1117'),
('王香君', 5, '1159', 3, '1159'),
('胡宜婷', 5, '1165', 3, '1165'),
('林宜靜', 5, '1163', 3, '1163'),
('陳慧娟', 5, '1389', 3, '1389'),
('黃雅芬', 5, '1162', 3, '1162'),
('金宗博', 5, '1111', 3, '1111'),
('林秀玲', 5, '1115', 3, '1115'),
('黃宏恩', 5, '1158', 3, '1158'),
('林秀羽', 5, '1164', 3, '1164'),
('鄭佳琳', 5, '1160', 3, '1160'),
('陳慧馨', 5, '1112', 3, '1112'),
('游麗君', 5, '1161', 3, '1161'),
('陳慧馨', 5, '1110', 3, '1110'),
('張美馨', 5, '1113', 3, '1113'),
('段瑋玲', 5, '3320', 3, '3320'),
('吳尊宇', 5, '3313', 3, '3313'),
('鄭純靜', 5, '3297', 3, '3297'),
('盧怡雅', 5, '3294', 3, '3294'),
('饒伯偉', 5, '3382', 3, '3382'),
('黃怡婷', 5, '3292', 3, '3292'),
('黃怡婷', 5, '3291', 3, '3291'),
('徐欣儀', 5, '2002', 3, '2002'),
('吳昊恩', 5, '2003', 3, '2003'),
('段安純', 5, '2001', 3, '2001'),
('周冠宏', 5, '1119', 3, '1119'),
('黃正安', 5, '3000', 3, '3000'),
('許訓輝', 5, '3296', 3, '3296'),
('許訓輝', 5, '3293', 3, '3293'),
('饒欣儀', 5, '3381', 3, '3381'),
('許訓輝', 5, '3387', 3, '3387');

-- 108-3號6F (出版單位)
INSERT INTO employee_seats (employee_name, floor_number, seat_number, department_id, extension_number) VALUES
('吳雅萍', 6, '1390', 2, '1390'),
('吳宜軒', 6, '1397', 2, '1397'),
('吳宜軒', 6, '1391', 2, '1391'),
('陳玉娟', 6, '1403', 2, '1403'),
('麥知芹', 6, '1392', 2, '1392'),
('陳玉娟', 6, '1399', 2, '1399'),
('陳玉娟', 6, '1393', 2, '1393'),
('陳玉娟', 6, '1396', 2, '1396'),
('徐雲漢', 6, '1395', 2, '1395'),
('陳玉娟', 6, '1394', 2, '1394'),
('陳欣儀', 6, '1404', 2, '1404'),
('高雅鈴', 6, '1398', 2, '1398'),
('劉萍', 6, '1402', 2, '1402'),
('陳玉娟', 6, '1405', 2, '1405'),
('林嘉怡', 6, '1265', 2, '1265'),
('林哲伯', 6, '3280', 2, '3280'),
('林哲伯', 6, '3281', 2, '3281'),
('林哲伯', 6, '1266', 2, '1266'),
('林哲伯', 6, '1271', 2, '1271'),
('林哲伯', 6, '1262', 2, '1262'),
('林哲伯', 6, '1260', 2, '1260'),
('林哲伯', 6, '3420', 2, '3420'),
('林哲伯', 6, '1269', 2, '1269'),
('黃文化', 6, '1232', 2, '1232'),
('藍善源', 6, '3165', 2, '3165'),
('林哲伯', 6, '1261', 2, '1261');

-- 108-3號8F (出版單位)
INSERT INTO employee_seats (employee_name, floor_number, seat_number, department_id, extension_number) VALUES
('黃威琳', 8, '3152', 2, '3152'),
('林育文', 8, '3153', 2, '3153'),
('賴慧怡', 8, '3154', 2, '3154'),
('林清文', 8, '3302', 2, '3302'),
('林育彤', 8, '3155', 2, '3155'),
('邱慧青', 8, '3311', 2, '3311'),
('張智琦', 8, '3461', 2, '3461'),
('范玉琪', 8, '3308', 2, '3308'),
('林秀怡', 8, '3216', 2, '3216'),
('張智琦', 8, '3304', 2, '3304'),
('張智琦', 8, '3303', 2, '3303'),
('徐育敏', 8, '3305', 2, '3305'),
('黃玉琴', 8, '3301', 2, '3301'),
('徐育敏', 8, '3310', 2, '3310'),
('張智琦', 8, '3321', 2, '3321'),
('天元', 8, '3383', 2, '3383'),
('鍾玉琴', 8, '3167', 2, '3167'),
('黃鈺婷', 8, '3170', 2, '3170'),
('黃鈺婷', 8, '3171', 2, '3171'),
('黃鈺婷', 8, '3172', 2, '3172'),
('成怡君', 8, '1286', 2, '1286'),
('陳宜萱', 8, '1287', 2, '1287'),
('李芯瑩', 8, '3160', 2, '3160'),
('邱建智', 8, '3168', 2, '3168'),
('蔡慧馨', 8, '3163', 2, '3163'),
('許月容', 8, '3164', 2, '3164'),
('李怡俊', 8, '3162', 2, '3162'),
('賴慧玲', 8, '1250', 2, '1250'),
('洪淑君', 8, '1253', 2, '1253'),
('楊淑如', 8, '1252', 2, '1252'),
('賴慧玲', 8, '1255', 2, '1255'),
('賴慧玲', 8, '1254', 2, '1254'),
('賴慧玲', 8, '1251', 2, '1251'),
('謝慧', 8, '3219', 2, '3219'),
('邱瑞城', 8, '3217', 2, '3217'),
('黃慧玲', 8, '3121', 2, '3121'),
('黃慧玲', 8, '3417', 2, '3417'),
('黃慧玲', 8, '3418', 2, '3418'),
('林昌德', 8, '3120', 2, '3120'),
('黃慧玲', 8, '3384', 2, '3384'),
('黃慧玲', 8, '1002', 2, '1002'),
('吳淑芳', 8, '3236', 2, '3236'),
('王浩瑋', 8, '3235', 2, '3235'),
('蘇慧卿', 8, '3232', 2, '3232'),
('黃秀如', 8, '3230', 2, '3230'),
('林巧伶', 8, '3233', 2, '3233'),
('徐慧齡', 8, '3231', 2, '3231'),
('劉佳容', 8, '3237', 2, '3237');

-- 108-4號8F (營業單位辦公區)
INSERT INTO employee_seats (employee_name, floor_number, seat_number, department_id, extension_number) VALUES
('陳建芳', 8, '1322', 3, '1322'),
('吳玉卿', 8, '1320', 3, '1320'),
('黃純鈺', 8, '1321', 3, '1321'),
('范子源', 8, '1139', 3, '1139'),
('許瑞昇', 8, '1323', 3, '1323'),
('黃純芳', 8, '1288', 3, '1288'),
('盧清婷', 8, '1120', 3, '1120'),
('陳欣瑩', 8, '1142', 3, '1142'),
('吳思惠', 8, '1302', 3, '1302'),
('葉政宏', 8, '1128', 3, '1128'),
('張詩芸', 8, '1521', 3, '1521'),
('黃美芳', 8, '1294', 3, '1294'),
('許雅玲', 8, '1313', 3, '1313'),
('高清芳', 8, '1141', 3, '1141'),
('李佳軒', 8, '1126', 3, '1126'),
('陳宜芳', 8, '1125', 3, '1125'),
('林柏芳', 8, '1124', 3, '1124'),
('黃心怡', 8, '1137', 3, '1137'),
('王雅明', 8, '1127', 3, '1127'),
('黃素芳', 8, '1136', 3, '1136'),
('鄭文超', 8, '1520', 3, '1520'),
('周秀蓮', 8, '1123', 3, '1123'),
('陳淑娟', 8, '1132', 3, '1132'),
('黃文弘', 8, '1129', 3, '1129'),
('張慧馨', 8, '1310', 3, '1310'),
('盧清婷', 8, '1316', 3, '1316'),
('黃文佳', 8, '1317', 3, '1317'),
('黃道賢', 8, '1510', 3, '1510'),
('李玉珊', 8, '1381', 3, '1381'),
('許清源', 8, '1511', 3, '1511');

-- 108-2號9F (出版單位 + 社長辦公室)
INSERT INTO employee_seats (employee_name, floor_number, seat_number, department_id, extension_number) VALUES
('郭重興', 9, '1100', 1, '1100'),
('呂佳怡', 9, '1186', 1, '1186'),
('陳昭馨', 9, '1180', 1, '1180'),
('李欣華', 9, '1181', 1, '1181'),
('林玫馨', 9, '1183', 1, '1183'),
('郭誠音', 9, '1182', 1, '1182'),
('陳昭馨', 9, '1188', 1, '1188'),
('林子楊', 9, '1191', 1, '1191'),
('鍾昭珊', 9, '1190', 1, '1190'),
('張展瑜', 9, '1185', 1, '1185'),
('林淑慧', 9, '3261', 1, '3261'),
('許文華', 9, '3265', 1, '3265'),
('李芳芳', 9, '3264', 1, '3264'),
('許文華', 9, '3262', 1, '3262'),
('張玲慧', 9, '3267', 1, '3267'),
('饒海波', 9, '3266', 1, '3266'),
('張慧馨', 9, '1270', 1, '1270'),
('李怡馨', 9, '1279', 1, '1279'),
('陳瑞陽', 9, '1273', 1, '1273'),
('蘇鈴培', 9, '1278', 1, '1278'),
('蔡欣怡', 9, '1267', 1, '1267'),
('莊靈郁', 9, '1275', 1, '1275'),
('莊靈郁', 9, '1277', 1, '1277'),
('林嘉紅', 9, '1274', 1, '1274'),
('安欣素', 9, '1280', 1, '1280'),
('蔡慧真', 9, '1272', 1, '1272'),
('徐子益', 9, '1231', 1, '1231'),
('謝怡文', 9, '1305', 1, '1305'),
('余文馨', 9, '1268', 1, '1268'),
('余文馨', 9, '1306', 1, '1306'),
('鄭欣儀', 9, '3450', 1, '3450');

-- 南崁物流中心
INSERT INTO employee_seats (employee_name, floor_number, seat_number, department_id, extension_number) VALUES
('左富銘', 1, '201', 8, '201'),
('謝持凡', 1, '203', 8, '203'),
('蘇林城', 1, '103', 8, '103'),
('工作桌', 1, '102', 8, '102'),
('張中恩', 2, '206', 8, '206'),
('工作桌', 2, '207', 8, '207'),
('工作桌', 2, '208', 8, '208');

-- 清空現有資料
TRUNCATE TABLE floor_info;

-- 插入樓層資料
INSERT INTO floor_info (floor_number, floor_name, floor_description, floor_type) VALUES
-- 總經理辦公室 (7樓)
(7, '總經理辦公室', '分機: 101\n信箱: ceo@bookrep.com.tw', '行政區'),

-- 編輯部 (6樓)
(6, '編輯部', '分機: 201-210\n信箱: editorial@bookrep.com.tw', '出版區'),

-- 業務部 (5樓)
(5, '業務部', '分機: 301-315\n信箱: sales@bookrep.com.tw', '營業區'),

-- 製作部 (4樓)
(4, '製作部', '分機: 401-410\n信箱: production@bookrep.com.tw', '出版區'),

-- 行銷部 (5樓)
(5, '行銷部', '分機: 316-325\n信箱: marketing@bookrep.com.tw', '營業區'),

-- 人事行政部 (3樓)
(3, '人事行政部', '分機: 501-505\n信箱: hr@bookrep.com.tw', '行政區'),

-- 財務會計部 (3樓)
(3, '財務會計部', '分機: 601-608\n信箱: finance@bookrep.com.tw', '行政區'),

-- 資訊部 (3樓)
(3, '資訊部', '分機: 701-705\n信箱: it@bookrep.com.tw', '行政區');

-- 更新樓層空位資訊
UPDATE floor_info SET vacant_seats = 3 
WHERE building_name = '108-3號' AND floor_number = 6;

UPDATE floor_info SET vacant_seats = 13 
WHERE building_name = '108-3號' AND floor_number = 8;

UPDATE floor_info SET vacant_seats = 49 
WHERE building_name = '108-4號' AND floor_number = 8;

-- 設置各樓層的部門分布
INSERT INTO floor_departments (floor_id, department_id) 
SELECT f.id, d.id 
FROM floors f 
CROSS JOIN departments d 
WHERE 
  (f.building_name = '108-3號' AND f.floor_number IN (3,6,8) AND d.id = 2) OR -- 編輯部
  (f.building_name = '108-4號' AND f.floor_number = 5 AND d.id IN (2,3)) OR -- 出版+營業
  (f.building_name = '108-4號' AND f.floor_number = 8 AND d.id = 3) OR -- 營業部
  (f.building_name = '108-2號' AND f.floor_number = 9 AND d.id = 1) OR -- 總經理室
  (f.building_name = '南崁物流中心' AND f.floor_number IN (1,2) AND d.id = 8); -- 資訊部

-- 設置樓層設施
INSERT INTO floor_facilities (floor_id, facility_name, facility_description)
SELECT f.id, '會議室', CASE 
    WHEN f.building_name = '108-3號' AND f.floor_number = 3 THEN '3A會議室'
    WHEN f.building_name = '108-3號' AND f.floor_number = 6 THEN '6A,6C會議室'
    WHEN f.building_name = '108-2號' AND f.floor_number = 9 THEN '9A會議室'
    ELSE '會議室'
END
FROM floors f
WHERE f.building_name IN ('108-3號', '108-2號') 
AND f.floor_number IN (3,6,9);

-- 新增其他設施
INSERT INTO floor_facilities (floor_id, facility_name, facility_description)
SELECT f.id, '茶水間', '茶水間'
FROM floors f
WHERE f.building_name IN ('108-3號', '108-4號', '108-2號');

INSERT INTO floor_facilities (floor_id, facility_name, facility_description)
SELECT f.id, '影印室', '影印室'
FROM floors f
WHERE f.building_name IN ('108-3號', '108-4號'); 