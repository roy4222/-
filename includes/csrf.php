<?php
session_start();

/**
 * 生成 CSRF token
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * 驗證 CSRF token
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    
    return true;
}

/**
 * 生成記住我的 token
 */
function generate_remember_token() {
    return bin2hex(random_bytes(32));
}

/**
 * 設置記住我的 cookie
 */
function set_remember_cookie($user_id, $token) {
    $expiry = time() + (30 * 24 * 60 * 60); // 30 days
    setcookie('remember_token', $token, $expiry, '/', '', true, true);
    setcookie('user_id', $user_id, $expiry, '/', '', true, true);
}

/**
 * 檢查登入嘗試次數限制
 */
function check_rate_limit($ip) {
    if (!isset($_SESSION['login_attempts'][$ip])) {
        $_SESSION['login_attempts'][$ip] = [
            'count' => 0,
            'first_attempt' => time()
        ];
    }

    $attempts = &$_SESSION['login_attempts'][$ip];
    
    // 重置計數器（如果超過30分鐘）
    if (time() - $attempts['first_attempt'] > 1800) {
        $attempts['count'] = 0;
        $attempts['first_attempt'] = time();
        return false;
    }
    
    // 如果嘗試次數超過5次
    return $attempts['count'] >= 5;
}

/**
 * 增加失敗嘗試次數
 */
function increment_failed_attempts($ip) {
    if (!isset($_SESSION['login_attempts'][$ip])) {
        $_SESSION['login_attempts'][$ip] = [
            'count' => 0,
            'first_attempt' => time()
        ];
    }
    
    $_SESSION['login_attempts'][$ip]['count']++;
}
