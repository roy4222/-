<?php
if (!function_exists('set_flash_message')) {
    function set_flash_message($type, $message) {
        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }
        $_SESSION['flash_messages'][$type] = $message;
    }
}

if (isset($_SESSION['flash_messages'])) {
    foreach ($_SESSION['flash_messages'] as $type => $message) {
        $alertClass = $type === 'error' ? 'danger' : $type;
        echo "<div class='alert alert-{$alertClass} alert-dismissible fade show' role='alert'>";
        echo htmlspecialchars($message);
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
        echo "</div>";
    }
    unset($_SESSION['flash_messages']);
}
