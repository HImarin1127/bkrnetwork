<?php
// MySQL 連線設定
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PWD',  '');
define('DB_NAME', 'ldap_app');

function db() {
    static $mysqli;
    if (!$mysqli) {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PWD, DB_NAME);
        if ($mysqli->connect_error) {
            die('資料庫連線錯誤：' . $mysqli->connect_error);
        }
        $mysqli->set_charset('utf8mb4');
    }
    return $mysqli;
}
?>