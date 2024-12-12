<?php
session_start();

// Update last login time before logging out
if (isset($_SESSION['PlayerID'])) {
    require_once 'config/database.php';
    $stmt = $pdo->prepare("UPDATE Player SET LastLoginDate = CURRENT_TIMESTAMP WHERE PlayerID = ?");
    $stmt->execute([$_SESSION['PlayerID']]);
}

// Destroy all session data
session_destroy();

// Clear session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Redirect to login page
header("Location: login.php");
exit();
?>
