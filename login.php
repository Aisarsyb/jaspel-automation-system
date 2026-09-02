<?php
declare(strict_types=1);

require_once 'app/config/config.php';

// Redirect if already logged in
Session::requireGuest();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi!';
    } else {
        $user = User::authenticate($username, $password);
        if ($user) {
            Session::set('user_id', $user->id);
            Session::set('username', $user->username);
            Session::set('role', $user->role);

            // Log activity
            Helper::logActivity('Login', 'Admin berhasil masuk ke sistem');

            header("Location: /Excel_Automation_System/modules/dashboard/index.php");
            exit();
        } else {
            $error = 'Username atau password salah!';
            // Log failed attempt
            error_log("Failed login attempt for username: {$username}");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | <?php echo Helper::sanitize(APP_NAME); ?></title>
    <link rel="stylesheet" href="/Excel_Automation_System/assets/css/style.css">
    <style>
        body {
            background: radial-gradient(circle at 50% 50%, var(--primary-light) 0%, var(--background) 100%);
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <div class="login-logo">
            <div class="login-logo-box">J</div>
            <div class="login-logo-name"><?php echo Helper::sanitize(APP_NAME); ?></div>
        </div>
        
        <div class="login-header">
            <h1 class="login-title">Selamat Datang</h1>
            <p class="login-subtitle">Silakan masuk menggunakan akun Administrator Anda</p>
        </div>

        <?php if ($error): ?>
            <div class="alert-banner alert-banner-danger" style="margin-bottom: 20px; padding: 12px 16px;">
                <div class="alert-banner-icon">⚠️</div>
                <div class="alert-banner-content">
                    <p style="font-size: 13px; font-weight: 500;"><?php echo Helper::sanitize($error); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
            </div>
            
            <div class="form-group" style="margin-bottom: 28px;">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">Masuk</button>
        </form>
    </div>
</div>
</body>
</html>
