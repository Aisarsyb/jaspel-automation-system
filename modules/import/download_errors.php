<?php
declare(strict_types=1);

require_once '../../app/config/config.php';

// Auth Check
Session::requireLogin();

$lastErrors = $_SESSION['import_last_errors'] ?? null;

if (!$lastErrors) {
    die("Tidak ada laporan error import terbaru yang tersedia.");
}

$fileName = 'ERROR_REPORT_' . str_replace('.xlsx', '', $lastErrors['file_name']) . '_' . date('dmy_His') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $fileName . '"');

$output = fopen('php://output', 'w');

// Output UTF-8 BOM for Excel compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Header
fputcsv($output, ['Baris Excel', 'Nama Dokter / DPJP', 'Pesan Error / Keterangan']);

// Data
foreach ($lastErrors['errors'] as $err) {
    fputcsv($output, [
        $err['row'],
        $err['doctor_name'],
        $err['message']
    ]);
}

fclose($output);
exit();
