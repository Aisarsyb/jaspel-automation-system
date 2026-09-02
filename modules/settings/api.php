<?php
declare(strict_types=1);

require_once '../../app/config/config.php';

// Auth Check
if (!Session::isLoggedIn()) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_GET['action'] ?? '';

// Trigger database SQL download (GET request, downloads attachment)
if ($action === 'backup_db') {
    try {
        $sqlDump = BackupService::exportSql();
        
        $fileName = 'backup_rsgm_jaspel_' . date('dmY_His') . '.sql';
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . strlen($sqlDump));
        
        Helper::logActivity('Backup Database', 'Admin mendownload salinan cadangan SQL database');
        echo $sqlDump;
        exit();
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Gagal membuat backup: ' . $e->getMessage()]);
        exit();
    }
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'health_check') {
        $health = [];

        // 1. Database Connection
        try {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT 1");
            $health['database'] = ['status' => true, 'message' => 'Koneksi database MariaDB aktif.'];
        } catch (Exception $e) {
            $health['database'] = ['status' => false, 'message' => 'Gagal terhubung: ' . $e->getMessage()];
        }

        // 2. Storage Permissions
        $storagePaths = [
            'imports/raw' => STORAGE_DIR . 'imports/raw',
            'imports/processed' => STORAGE_DIR . 'imports/processed',
            'exports' => STORAGE_DIR . 'exports',
            'errors' => STORAGE_DIR . 'errors',
            'temp' => STORAGE_DIR . 'temp',
            'logs' => STORAGE_DIR . 'logs'
        ];

        $health['storage'] = [];
        $storageSuccess = true;
        $unwritable = [];

        foreach ($storagePaths as $key => $path) {
            if (!is_dir($path)) {
                @mkdir($path, 0777, true);
            }
            if (!is_writable($path)) {
                $storageSuccess = false;
                $unwritable[] = "storage/{$key}";
            }
        }

        if ($storageSuccess) {
            $health['storage'] = ['status' => true, 'message' => 'Semua direktori penyimpanan dapat ditulis (writable).'];
        } else {
            $health['storage'] = ['status' => false, 'message' => 'Direktori berikut tidak dapat ditulis: ' . implode(', ', $unwritable)];
        }

        // 3. PHP Extensions
        $reqExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'xml', 'zip', 'gd'];
        $missingExt = [];
        foreach ($reqExtensions as $ext) {
            if (!extension_loaded($ext)) {
                $missingExt[] = $ext;
            }
        }

        if (empty($missingExt)) {
            $health['php_extensions'] = ['status' => true, 'message' => 'Semua ekstensi PHP yang dibutuhkan aktif.'];
        } else {
            $health['php_extensions'] = ['status' => false, 'message' => 'Ekstensi PHP berikut tidak aktif: ' . implode(', ', $missingExt)];
        }

        // 4. PhpSpreadsheet Status
        if (class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            $health['phpspreadsheet'] = ['status' => true, 'message' => 'Library PhpSpreadsheet berhasil dimuat.'];
        } else {
            $health['phpspreadsheet'] = ['status' => false, 'message' => 'Library PhpSpreadsheet tidak ditemukan di vendor/.'];
        }

        // Overall status
        $isOk = $health['database']['status'] && $health['storage']['status'] && $health['php_extensions']['status'] && $health['phpspreadsheet']['status'];

        echo json_encode([
            'success' => true,
            'is_healthy' => $isOk,
            'checks' => $health
        ]);
        exit();
    }

    if ($action === 'logs') {
        try {
            $db = Database::getConnection();
            
            // Get audit logs
            $stmtAudit = $db->query("SELECT a.*, u.username FROM audit_logs a JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT 50");
            $auditLogs = $stmtAudit->fetchAll();

            // Get system logs
            $stmtSystem = $db->query("SELECT * FROM system_logs ORDER BY created_at DESC LIMIT 50");
            $systemLogs = $stmtSystem->fetchAll();

            echo json_encode([
                'success' => true,
                'audit_logs' => $auditLogs,
                'system_logs' => $systemLogs
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal memuat log: ' . $e->getMessage()]);
        }
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = [];
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (strpos($contentType, 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
    } else {
        $input = $_POST;
    }

    if ($action === 'update_settings') {
        $settings = $input['settings'] ?? [];

        if (empty($settings)) {
            echo json_encode(['success' => false, 'message' => 'Data pengaturan kosong!']);
            exit();
        }

        try {
            $db = Database::getConnection();
            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                                  ON DUPLICATE KEY UPDATE setting_value = ?");
            
            foreach ($settings as $key => $val) {
                // Basic validations
                $val = trim((string)$val);
                if ($key === 'JASPEL_PERCENTAGE' || $key === 'RKG_JASPEL_PERCENTAGE') {
                    $numVal = (float)$val;
                    if ($numVal < 0 || $numVal > 100) {
                        throw new InvalidArgumentException("Persentase Jaspel harus bernilai antara 0% dan 100%!");
                    }
                }
                
                $stmt->execute([$key, $val, $val]);
            }

            $db->commit();
            Helper::logActivity('Update Settings', 'Memperbarui konfigurasi sistem');
            echo json_encode(['success' => true, 'message' => 'Pengaturan berhasil diperbarui.']);
        } catch (InvalidArgumentException $ex) {
            if ($db->inTransaction()) $db->rollBack();
            echo json_encode(['success' => false, 'message' => $ex->getMessage()]);
        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            Helper::logSystemError("Update settings failure: " . $e->getMessage(), $e->getTraceAsString());
            echo json_encode(['success' => false, 'message' => 'Error saat menyimpan pengaturan: ' . $e->getMessage()]);
        }
        exit();
    }

    if ($action === 'restore_db') {
        if (!isset($_FILES['restore_file']) || $_FILES['restore_file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'File SQL wajib diupload!']);
            exit();
        }

        $fileTmpPath = $_FILES['restore_file']['tmp_name'];
        $sqlContent = file_get_contents($fileTmpPath);

        if ($sqlContent === false || trim($sqlContent) === '') {
            echo json_encode(['success' => false, 'message' => 'File SQL kosong atau tidak dapat dibaca!']);
            exit();
        }

        $res = BackupService::restoreSql($sqlContent);
        if ($res) {
            Helper::logActivity('Restore Database', 'Berhasil merestorasi database dari file SQL cadangan');
            echo json_encode(['success' => true, 'message' => 'Database berhasil direstore!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal merestore database. Periksa kecocokan file SQL dump.']);
        }
        exit();
    }
}

header('HTTP/1.1 400 Bad Request');
echo json_encode(['success' => false, 'message' => 'Action atau request method tidak valid']);
exit();
