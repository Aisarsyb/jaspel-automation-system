<?php
declare(strict_types=1);

require_once '../../app/config/config.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

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
        $departmentId = (int)($_GET['department_id'] ?? 0);
        $data = Doctor::getAll($search, $departmentId);
        echo json_encode(['success' => true, 'data' => $data]);
        exit();
    }

    if ($action === 'get_aliases') {
        $doctorId = (int)($_GET['doctor_id'] ?? 0);
        if ($doctorId <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID dokter tidak valid']);
            exit();
        }
        $aliases = Doctor::getAliases($doctorId);
        echo json_encode(['success' => true, 'aliases' => $aliases]);
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

    if ($action === 'create') {
        $name = trim($input['doctor_name'] ?? '');
        $departmentId = (int)($input['department_id'] ?? 0);

        if ($name === '' || $departmentId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Nama dokter dan Departemen wajib diisi!']);
            exit();
        }

        // Check if doctor name already exists under the same department
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM dpjp WHERE doctor_name = ? AND department_id = ?");
        $stmt->execute([$name, $departmentId]);
        if ((int)$stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Nama dokter sudah ada di departemen ini!']);
            exit();
        }

        $res = Doctor::create($name, $departmentId);
        if ($res) {
            Helper::logActivity('Create DPJP', "Menambah DPJP baru: {$name} (Dept ID: {$departmentId})");
            echo json_encode(['success' => true, 'message' => 'Dokter berhasil ditambahkan.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan dokter.']);
        }
        exit();
    }

    if ($action === 'update') {
        $id = (int)($input['id'] ?? 0);
        $name = trim($input['doctor_name'] ?? '');
        $departmentId = (int)($input['department_id'] ?? 0);

        if ($id <= 0 || $name === '' || $departmentId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Semua kolom wajib diisi!']);
            exit();
        }

        // Validate duplicates excluding current
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM dpjp WHERE doctor_name = ? AND department_id = ? AND id != ?");
        $stmt->execute([$name, $departmentId, $id]);
        if ((int)$stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Nama dokter sudah terdaftar di departemen ini!']);
            exit();
        }

        $res = Doctor::update($id, $name, $departmentId);
        if ($res) {
            Helper::logActivity('Update DPJP', "Memperbarui DPJP ID {$id}: {$name} (Dept ID: {$departmentId})");
            echo json_encode(['success' => true, 'message' => 'Data dokter berhasil diperbarui.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data dokter.']);
        }
        exit();
    }

    if ($action === 'delete') {
        $id = (int)($input['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid!']);
            exit();
        }

        $doc = Doctor::getById($id);
        $name = $doc ? $doc['doctor_name'] : (string)$id;

        $res = Doctor::delete($id);
        if ($res) {
            Helper::logActivity('Delete DPJP', "Menghapus DPJP: {$name}");
            echo json_encode(['success' => true, 'message' => 'Dokter berhasil dihapus.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus dokter.']);
        }
        exit();
    }

    if ($action === 'add_alias') {
        $doctorId = (int)($input['doctor_id'] ?? 0);
        $aliasName = trim($input['alias_name'] ?? '');

        if ($doctorId <= 0 || $aliasName === '') {
            echo json_encode(['success' => false, 'message' => 'ID dokter dan Nama Alias wajib diisi!']);
            exit();
        }

        $res = Doctor::addAlias($doctorId, $aliasName);
        if ($res) {
            Helper::logActivity('Add DPJP Alias', "Menambah alias '{$aliasName}' untuk dokter ID {$doctorId}");
            echo json_encode(['success' => true, 'message' => 'Alias berhasil ditambahkan.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambah alias. Alias mungkin sudah ada / terdaftar untuk dokter lain.']);
        }
        exit();
    }

    if ($action === 'delete_alias') {
        $aliasId = (int)($input['alias_id'] ?? 0);
        if ($aliasId <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID alias tidak valid']);
            exit();
        }

        $res = Doctor::deleteAlias($aliasId);
        if ($res) {
            echo json_encode(['success' => true, 'message' => 'Alias berhasil dihapus.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus alias.']);
        }
        exit();
    }

    if ($action === 'import_excel') {
        if (!isset($_FILES['doctors_file']) || $_FILES['doctors_file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'File Excel wajib diupload!']);
            exit();
        }

        $fileTmpPath = $_FILES['doctors_file']['tmp_name'];
        $fileName = $_FILES['doctors_file']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileExtension !== 'xlsx') {
            echo json_encode(['success' => false, 'message' => 'Hanya file dengan ekstensi .xlsx yang diperbolehkan!']);
            exit();
        }

        try {
            $spreadsheet = IOFactory::load($fileTmpPath);
            $sheet = $spreadsheet->getActiveSheet();
            $maxRow = $sheet->getHighestRow();
            $maxCol = $sheet->getHighestColumn();
            $maxColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);

            // 1. Identify columns
            $colMapping = [
                'doctor' => -1,
                'department' => -1
            ];

            for ($col = 1; $col <= $maxColIndex; $col++) {
                $headerVal = strtolower(trim((string)$sheet->getCell([$col, 1])->getValue()));
                if (in_array($headerVal, ['nama dokter', 'dokter', 'dpjp', 'doctor', 'doctor_name'])) {
                    $colMapping['doctor'] = $col;
                }
                if (in_array($headerVal, ['departemen', 'department', 'spesialis', 'unit', 'department_name'])) {
                    $colMapping['department'] = $col;
                }
            }

            if ($colMapping['doctor'] === -1 || $colMapping['department'] === -1) {
                echo json_encode(['success' => false, 'message' => 'Format kolom header tidak sesuai. Pastikan ada kolom Nama Dokter dan Departemen pada baris pertama.']);
                exit();
            }

            $db = Database::getConnection();
            $db->beginTransaction();

            $insertCount = 0;
            $duplicateCount = 0;

            for ($row = 2; $row <= $maxRow; $row++) {
                $docName = trim((string)$sheet->getCell([$colMapping['doctor'], $row])->getValue());
                $deptName = trim((string)$sheet->getCell([$colMapping['department'], $row])->getValue());

                if ($docName === '' || $deptName === '') {
                    continue;
                }

                // 1. Get or Create Department
                $stmtDept = $db->prepare("SELECT id FROM departments WHERE department_name = ?");
                $stmtDept->execute([$deptName]);
                $deptId = $stmtDept->fetchColumn();

                if (!$deptId) {
                    $stmtInsertDept = $db->prepare("INSERT INTO departments (department_name) VALUES (?)");
                    $stmtInsertDept->execute([$deptName]);
                    $deptId = $db->lastInsertId();
                }

                // 2. Check if doctor already exists
                $stmtDocCheck = $db->prepare("SELECT id FROM dpjp WHERE doctor_name = ? AND department_id = ?");
                $stmtDocCheck->execute([$docName, $deptId]);
                $exists = $stmtDocCheck->fetchColumn();

                if ($exists) {
                    $duplicateCount++;
                    continue;
                }

                // 3. Create Doctor
                $stmtInsertDoc = $db->prepare("INSERT INTO dpjp (doctor_name, department_id) VALUES (?, ?)");
                $stmtInsertDoc->execute([$docName, $deptId]);
                $doctorId = $db->lastInsertId();

                // 4. Create default alias
                $normalized = Doctor::normalizeName($docName);
                $stmtAliasCheck = $db->prepare("SELECT COUNT(*) FROM dpjp_aliases WHERE alias_name = ?");
                $stmtAliasCheck->execute([$normalized]);
                if ((int)$stmtAliasCheck->fetchColumn() === 0) {
                    $stmtInsertAlias = $db->prepare("INSERT INTO dpjp_aliases (dpjp_id, alias_name) VALUES (?, ?)");
                    $stmtInsertAlias->execute([$doctorId, $normalized]);
                }

                $insertCount++;
            }

            $db->commit();
            Helper::logActivity('Import DPJP Excel', "Mengimpor dokter dari Excel. Sukses: {$insertCount}, Lewat (duplikat): {$duplicateCount}");
            echo json_encode([
                'success' => true,
                'message' => "Impor selesai. Berhasil menambahkan {$insertCount} dokter baru. {$duplicateCount} dilewati karena sudah terdaftar."
            ]);
            exit();

        } catch (Exception $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            Helper::logSystemError("Import doctors Excel failed: " . $e->getMessage(), $e->getTraceAsString());
            echo json_encode(['success' => false, 'message' => 'Gagal memproses file Excel: ' . $e->getMessage()]);
            exit();
        }
    }
}

header('HTTP/1.1 400 Bad Request');
echo json_encode(['success' => false, 'message' => 'Action atau request method tidak valid']);
exit();
