<?php
declare(strict_types=1);

/**
 * Database Connection Helper using PDO.
 */
class Database {
    private static ?PDO $connection = null;

    public static function getConnection(): PDO {
        if (self::$connection === null) {
            $host = 'localhost';
            $dbName = 'rsgm_jaspel';
            $username = 'root';
            $password = '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$host};dbname={$dbName};charset={$charset}";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$connection = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                // In a production context, log this error to system_logs.
                // We will attempt to write to system_logs if possible, but connection is failed.
                error_log("Database connection failed: " . $e->getMessage());
                throw new RuntimeException("Database connection error: " . $e->getMessage(), (int)$e->getCode(), $e);
            }
        }

        return self::$connection;
    }
}
