<?php
declare(strict_types=1);

require_once 'app/config/config.php';

// Paksa set session agar sistem mengira user selalu berhasil login (Bypass Login)
Session::set('user_id', 1);
Session::set('username', 'admin');
Session::set('role', 'admin');

// Langsung arahkan (redirect) ke halaman dashboard tanpa mengecek form
header("Location: /Excel_Automation_System/modules/dashboard/index.php");
exit();
?>
