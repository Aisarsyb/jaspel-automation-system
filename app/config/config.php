<?php
declare(strict_types=1);

// Enable full error reporting in development
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Define directory constants
define('ROOT_DIR', dirname(__DIR__, 2) . '/');
define('APP_DIR', ROOT_DIR . 'app/');
define('STORAGE_DIR', ROOT_DIR . 'storage/');

// Load Composer Autoloader
if (file_exists(ROOT_DIR . 'vendor/autoload.php')) {
    require_once ROOT_DIR . 'vendor/autoload.php';
}

// Register Class Autoloader
spl_autoload_register(function ($class) {
    // Directories to search for classes
    $directories = [
        APP_DIR . 'services/',
        APP_DIR . 'helpers/',
        APP_DIR . 'models/',
        APP_DIR . 'config/',
    ];

    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Load DB connection helper
require_once APP_DIR . 'config/database.php';

// Fetch dynamic configurations from database settings
try {
    $db = Database::getConnection();
    $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    define('APP_NAME', $settings['APP_NAME'] ?? 'RSGM Jaspel');
    define('COMPANY_NAME', $settings['COMPANY'] ?? 'RSGM Universitas Airlangga');
    define('JASPEL_PERCENTAGE', (float)($settings['JASPEL_PERCENTAGE'] ?? 20.0));
    define('RKG_DEPT_NAME', 'Radiologi Kedokteran Gigi');
    define('RKG_JASPEL_PERCENTAGE', (float)($settings['RKG_JASPEL_PERCENTAGE'] ?? 15.0));
    define('RKG_DPJP_LABEL', 'DPJP RKG');
    define('MAX_UPLOAD_SIZE', (int)($settings['MAX_UPLOAD_SIZE'] ?? 20));
    define('ALLOWED_EXTENSION', $settings['ALLOWED_EXTENSION'] ?? 'xlsx');
} catch (Exception $e) {
    // Default fallback constants in case DB is not yet initialized or connected
    define('APP_NAME', 'RSGM Jaspel');
    define('COMPANY_NAME', 'RSGM Universitas Airlangga');
    define('JASPEL_PERCENTAGE', 20.0);
    define('RKG_DEPT_NAME', 'Radiologi Kedokteran Gigi');
    define('RKG_JASPEL_PERCENTAGE', 15.0);
    define('RKG_DPJP_LABEL', 'DPJP RKG');
    define('MAX_UPLOAD_SIZE', 20);
    define('ALLOWED_EXTENSION', 'xlsx');
}

// Start Session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
