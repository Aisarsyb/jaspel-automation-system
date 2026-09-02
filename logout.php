<?php
declare(strict_types=1);

require_once 'app/config/config.php';

if (Session::isLoggedIn()) {
    Helper::logActivity('Logout', 'Admin keluar dari sistem');
}

// Destroy all session data
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

header("Location: /Excel_Automation_System/login.php");
exit();
