<?php
declare(strict_types=1);

require_once '../../app/config/config.php';

// Auth Check
if (!Session::isLoggedIn()) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'list') {
        $search = trim($_GET['search'] ?? '');
        $filterDate = $_GET['filter_date'] ?? 'all';

        try {
            $db = Database::getConnection();

            // 1. Build Query
            $query = "SELECT h.*, u.username FROM import_history h 
                      JOIN users u ON h.imported_by = u.id";
            
            $where = [];
            $params = [];

            if ($search !== '') {
                $where[] = "(h.file_name LIKE ? OR h.output_file LIKE ?)";
                $params[] = '%' . $search . '%';
                $params[] = '%' . $search . '%';
            }

            if ($filterDate === 'today') {
                $where[] = "DATE(h.created_at) = CURRENT_DATE";
            } elseif ($filterDate === 'week') {
                $where[] = "h.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            } elseif ($filterDate === 'month') {
                $where[] = "h.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            } elseif ($filterDate === 'year') {
                $where[] = "h.created_at >= DATE_SUB(NOW(), INTERVAL 365 DAY)";
            }

            if (!empty($where)) {
                $query .= " WHERE " . implode(" AND ", $where);
            }

            $query .= " ORDER BY h.created_at DESC";

            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $list = $stmt->fetchAll();

            // 2. Fetch statistics for cards based on current query parameters or overall
            $stmtStats = $db->query("
                SELECT 
                    COUNT(id) AS total_import,
                    COUNT(DISTINCT file_name) AS total_files,
                    SUM(total_rows) AS total_data,
                    SUM(total_jaspel) AS total_jaspel
                FROM import_history
            ");
            $stats = $stmtStats->fetch() ?: [
                'total_import' => 0,
                'total_files' => 0,
                'total_data' => 0,
                'total_jaspel' => 0.00
            ];

            echo json_encode([
                'success' => true,
                'data'    => $list,
                'stats'   => [
                    'total_import' => (int)($stats['total_import'] ?? 0),
                    'total_files'  => (int)($stats['total_files'] ?? 0),
                    'total_data'   => (int)($stats['total_data'] ?? 0),
                    'total_jaspel' => (float)($stats['total_jaspel'] ?? 0.00),
                    'formatted_jaspel' => Helper::formatRupiah((float)($stats['total_jaspel'] ?? 0.00))
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal memuat riwayat: ' . $e->getMessage()]);
        }
        exit();
    }

    if ($action === 'detail') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID riwayat tidak valid!']);
            exit();
        }

        try {
            $db = Database::getConnection();
            
            // Fetch history item
            $stmt = $db->prepare("SELECT h.*, u.username FROM import_history h JOIN users u ON h.imported_by = u.id WHERE h.id = ? LIMIT 1");
            $stmt->execute([$id]);
            $history = $stmt->fetch();

            if (!$history) {
                echo json_encode(['success' => false, 'message' => 'Riwayat tidak ditemukan!']);
                exit();
            }

            // Fetch errors associated
            $stmtErrors = $db->prepare("SELECT * FROM import_errors WHERE history_id = ? ORDER BY row_number ASC");
            $stmtErrors->execute([$id]);
            $errors = $stmtErrors->fetchAll();

            echo json_encode([
                'success' => true,
                'history' => $history,
                'errors'  => $errors
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal memuat detail riwayat: ' . $e->getMessage()]);
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

    if ($action === 'delete') {
        $id = (int)($input['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid!']);
            exit();
        }

        try {
            $db = Database::getConnection();

            // 1. Get filenames to delete physically
            $stmtFile = $db->prepare("SELECT file_name, output_file FROM import_history WHERE id = ?");
            $stmtFile->execute([$id]);
            $historyRow = $stmtFile->fetch();

            if ($historyRow) {
                $origName = $historyRow['file_name'];
                $outFile = $historyRow['output_file'];
                
                // Reconstruct timestamps from output_file: e.g. REKAP_JASPEL_1783223473_filename.xlsx
                // We split by underscore
                $parts = explode('_', $outFile);
                $timestamp = $parts[2] ?? '';

                $exportPath = STORAGE_DIR . 'exports/' . $outFile;
                $zipPath = STORAGE_DIR . 'exports/' . str_replace('.xlsx', '.zip', $outFile);
                $processedPath = STORAGE_DIR . 'imports/processed/' . $outFile;
                
                // Delete physical files
                if (file_exists($exportPath)) @unlink($exportPath);
                if (file_exists($zipPath)) @unlink($zipPath);
                if (file_exists($processedPath)) @unlink($processedPath);

                // Find input raw file: e.g. INPUT_JASPEL_<TIMESTAMP>_<ORIGNAL_NAME>
                if ($timestamp !== '') {
                    $rawDir = STORAGE_DIR . 'imports/raw/';
                    $files = glob($rawDir . "INPUT_JASPEL_{$timestamp}_*");
                    if ($files) {
                        foreach ($files as $f) {
                            @unlink($f);
                        }
                    }
                }

                // 2. Delete DB records (errors table will cascade delete automatically due to foreign key constraints)
                $stmtDel = $db->prepare("DELETE FROM import_history WHERE id = ?");
                $stmtDel->execute([$id]);

                Helper::logActivity('Delete History', "Menghapus riwayat import ID {$id} (File: {$origName})");
                echo json_encode(['success' => true, 'message' => 'Riwayat beserta file terkait berhasil dihapus.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Data riwayat tidak ditemukan.']);
            }
        } catch (Exception $e) {
            Helper::logSystemError("Delete import history failure: " . $e->getMessage(), $e->getTraceAsString());
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus riwayat: ' . $e->getMessage()]);
        }
        exit();
    }
}

header('HTTP/1.1 400 Bad Request');
echo json_encode(['success' => false, 'message' => 'Action atau request method tidak valid']);
exit();
