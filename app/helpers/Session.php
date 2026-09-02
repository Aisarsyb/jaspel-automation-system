<?php
declare(strict_types=1);

class Session {
    /**
     * Set a session variable.
     */
    public static function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session variable.
     */
    public static function get(string $key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Delete a session variable.
     */
    public static function remove(string $key): void {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Check if user is logged in.
     */
    public static function isLoggedIn(): bool {
        return self::get('user_id') !== null;
    }

    /**
     * Get logged-in user id.
     */
    public static function getUserId(): ?int {
        $id = self::get('user_id');
        return $id !== null ? (int)$id : null;
    }

    /**
     * Get logged-in username.
     */
    public static function getUsername(): ?string {
        return self::get('username');
    }

    /**
     * Require the user to be logged in, otherwise redirect.
     */
    public static function requireLogin(string $redirectUrl = '/Excel_Automation_System/login.php'): void {
        if (!self::isLoggedIn()) {
            header("Location: " . $redirectUrl);
            exit();
        }
    }

    /**
     * Require guest status (not logged in), otherwise redirect to dashboard.
     */
    public static function requireGuest(string $redirectUrl = '/Excel_Automation_System/modules/dashboard/index.php'): void {
        if (self::isLoggedIn()) {
            header("Location: " . $redirectUrl);
            exit();
        }
    }

    /**
     * Set a flash message.
     */
    public static function setFlash(string $type, string $message): void {
        $_SESSION['flash'][$type] = $message;
    }

    /**
     * Get and clear flash messages.
     */
    public static function getFlash(): array {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flash;
    }
}
