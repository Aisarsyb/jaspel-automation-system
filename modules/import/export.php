<?php
declare(strict_types=1);

require_once '../../app/config/config.php';

// Auth check
Session::requireLogin();

$fileName = $_GET['file'] ?? '';
$format = $_GET['format'] ?? 'excel';

if ($fileName === '') {
    die("Parameter file wajib diisi.");
}

// Security check to prevent directory traversal
$fileName = basename($fileName);

// Check prefix to ensure it belongs to jaspel outputs
if (!str_starts_with($fileName, 'REKAP_JASPEL_') && !str_starts_with($fileName, 'Temp_')) {
    die("Akses file tidak diizinkan.");
}

if ($format === 'zip') {
    // Replace .xlsx extension with .zip
    $zipName = str_replace('.xlsx', '.zip', $fileName);
    $filePath = STORAGE_DIR . 'exports/' . $zipName;
    $downloadName = $zipName;
    $contentType = 'application/zip';
} elseif ($format === 'doctor_zip') {
    $doctorZipName = str_replace('REKAP_JASPEL_', 'REKAP_JASPEL_PER_DOKTER_', str_replace('.xlsx', '.zip', $fileName));
    $filePath = STORAGE_DIR . 'exports/' . $doctorZipName;
    $downloadName = $doctorZipName;
    $contentType = 'application/zip';
} elseif ($format === 'temp_excel') {
    $filePath = STORAGE_DIR . 'exports/' . $fileName;
    $downloadName = $fileName;
    $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
} else {
    $filePath = STORAGE_DIR . 'exports/' . $fileName;
    $downloadName = $fileName;
    $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
}

if (!file_exists($filePath)) {
    // Fallback: check in storage/imports/processed/
    if ($format === 'excel') {
        $filePath = STORAGE_DIR . 'imports/processed/' . $fileName;
    }
}

if (file_exists($filePath)) {
    // Log activity
    Helper::logActivity('Download Output', "Mendownload file hasil rekap: {$downloadName} ({$format})");

    header('Content-Description: File Transfer');
    header('Content-Type: ' . $contentType);
    header('Content-Disposition: attachment; filename="' . $downloadName . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit();
} else {
    die("File tidak ditemukan di server. Mungkin file sudah terhapus.");
}
