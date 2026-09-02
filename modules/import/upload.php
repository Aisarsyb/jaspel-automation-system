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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupload file. Pastikan ukuran file di bawah batas php.ini.']);
        exit();
    }

    $file = $_FILES['excel_file'];
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmpPath = $file['tmp_name'];

    // Validate Extension
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if ($ext !== ALLOWED_EXTENSION) {
        echo json_encode(['success' => false, 'message' => 'Hanya file dengan ekstensi .' . ALLOWED_EXTENSION . ' yang diperbolehkan!']);
        exit();
    }

    // Validate size (MAX_UPLOAD_SIZE is in MB)
    $maxSizeBytes = MAX_UPLOAD_SIZE * 1024 * 1024;
    if ($fileSize > $maxSizeBytes) {
        echo json_encode(['success' => false, 'message' => 'Ukuran file (' . Helper::formatSize($fileSize) . ') melebihi batas maksimal (' . MAX_UPLOAD_SIZE . ' MB)!']);
        exit();
    }

    // Save to temp folder
    $tempName = 'temp_jaspel_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $destPath = STORAGE_DIR . 'temp/' . $tempName;

    if (!is_dir(STORAGE_DIR . 'temp')) {
        mkdir(STORAGE_DIR . 'temp', 0777, true);
    }

    if (move_uploaded_file($fileTmpPath, $destPath)) {
        echo json_encode([
            'success'   => true,
            'temp_name' => $tempName,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'formatted_size' => Helper::formatSize($fileSize)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memindahkan file ke folder sementara. Periksa hak akses folder storage/temp.']);
    }
    exit();
}

header('HTTP/1.1 400 Bad Request');
echo json_encode(['success' => false, 'message' => 'Invalid request method']);
exit();
