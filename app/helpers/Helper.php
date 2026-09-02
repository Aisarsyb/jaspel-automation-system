<?php
declare(strict_types=1);

class Helper {
    /**
     * Format number into Indonesian Rupiah currency format.
     */
    public static function formatRupiah(float $amount): string {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Sanitize input for output in HTML.
     */
    public static function sanitize(string $input): string {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Log user activities (Audit logs).
     */
    public static function logActivity(string $action, string $details): void {
        try {
            $db = Database::getConnection();
            $userId = Session::getUserId();
            if ($userId === null) {
                // If user is not logged in, but action happens (e.g. failed login attempt)
                // We use system user 1 or null (handled via query)
                $userId = 1; 
            }
            
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

            $stmt = $db->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $action, $details, $ipAddress]);
        } catch (Exception $e) {
            error_log("Failed to log activity: " . $e->getMessage());
        }
    }

    /**
     * Log system errors (System logs).
     */
    public static function logSystemError(string $message, string $stackTrace = '', string $severity = 'error'): void {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO system_logs (severity, error_message, stack_trace) VALUES (?, ?, ?)");
            $stmt->execute([$severity, $message, $stackTrace]);
        } catch (Exception $e) {
            error_log("Failed to log system error: " . $e->getMessage());
        }
    }

    /**
     * Format file size (e.g., in MB or KB).
     */
    public static function formatSize(float $bytes): string {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Format duration into seconds with unit.
     */
    public static function formatDuration(float $seconds): string {
        return number_format($seconds, 2) . ' detik';
    }

    /**
     * Escape string for JavaScript attribute/string placement.
     */
    public static function escapeJavaScript(string $input): string {
        return addslashes($input);
    }
}
