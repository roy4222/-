<?php
// 數據庫連接配置
$db_socket = getenv('DB_SOCKET') ?: null;
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_name = getenv('DB_NAME') ?: 'game_platform';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: '27003378';

// 設置時區
date_default_timezone_set('Asia/Taipei');

// 設置字符集
ini_set('default_charset', 'UTF-8');

try {
    if ($db_socket) {
        // Cloud SQL Unix socket
        $dsn = sprintf(
            "mysql:dbname=%s;unix_socket=%s",
            $db_name,
            $db_socket
        );
    } else {
        // 標準TCP連接
        $dsn = sprintf(
            "mysql:host=%s;dbname=%s;charset=utf8mb4",
            $db_host,
            $db_name
        );
    }

    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("數據庫連接失敗: " . $e->getMessage());
    http_response_code(500);
    die("服務暫時不可用");
}
?>
