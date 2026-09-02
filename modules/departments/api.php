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
        $data = Department::getAll($search);
        echo json_encode(['success' => true, 'data' => $data]);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read JSON body if content type is json
    $input = [];
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (strpos($contentType, 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
    } else {
        $input = $_POST;
    }

    if ($action === 'create') {
        $name = trim($input['department_name'] ?? '');
        $status = trim($input['status'] ?? 'active');

        if ($name === '') {
            echo json_encode(['success' => false, 'message' => 'Nama departemen tidak boleh kosong!']);
            exit();
        }

        // Validate duplicates
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM departments WHERE department_name = ?");
        $stmt->execute([$name]);
        if ((int)$stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Nama departemen sudah ada!']);
            exit();
        }

        $res = Department::create($name, $status);
        if ($res) {
            Helper::logActivity('Create Department', "Menambah departemen baru: {$name}");
            echo json_encode(['success' => true, 'message' => 'Departemen berhasil ditambahkan.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan departemen.']);
        }
        exit();
    }

    if ($action === 'update') {
        $id = (int)($input['id'] ?? 0);
        $name = trim($input['department_name'] ?? '');
        $status = trim($input['status'] ?? 'active');

        if ($id <= 0 || $name === '') {
            echo json_encode(['success' => false, 'message' => 'ID dan Nama departemen wajib diisi!']);
            exit();
        }

        // Validate duplicates excluding current
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM departments WHERE department_name = ? AND id != ?");
        $stmt->execute([$name, $id]);
        if ((int)$stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Nama departemen sudah digunakan oleh departemen lain!']);
            exit();
        }

        $res = Department::update($id, $name, $status);
        if ($res) {
            Helper::logActivity('Update Department', "Memperbarui departemen ID {$id} menjadi: {$name} ({$status})");
            echo json_encode(['success' => true, 'message' => 'Departemen berhasil diperbarui.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui departemen.']);
        }
        exit();
    }

    if ($action === 'delete') {
        $id = (int)($input['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid!']);
            exit();
        }

        // Get department name for log
        $dept = Department::getById($id);
        $name = $dept ? $dept['department_name'] : (string)$id;

        $res = Department::delete($id);
        if ($res['success']) {
            Helper::logActivity('Delete Department', "Menghapus departemen: {$name}");
        }
        echo json_encode($res);
        exit();
    }
}

header('HTTP/1.1 400 Bad Request');
echo json_encode(['success' => false, 'message' => 'Action atau request method tidak valid']);
exit();
