<?php
declare(strict_types=1);

require_once 'app/config/config.php';

// Route based on session login state
if (Session::isLoggedIn()) {
    header("Location: /Excel_Automation_System/modules/dashboard/index.php");
} else {
    header("Location: /Excel_Automation_System/login.php");
}
exit();
