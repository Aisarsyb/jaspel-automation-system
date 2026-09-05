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

$action = $_GET['action'] ?? 'process_temp';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'get_active_session') {
        if (isset($_SESSION['active_import_rows'])) {
            echo json_encode([
                'success' => true,
                'has_active_session' => true,
                'data' => generatePreviewData()
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'has_active_session' => false
            ]);
        }
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read JSON body
    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    if ($action === 'process_temp') {
        $tempName = trim($input['temp_name'] ?? '');
        $originalName = trim($input['file_name'] ?? '');

        if ($tempName === '' || $originalName === '') {
            echo json_encode(['success' => false, 'message' => 'Data parameter import tidak lengkap!']);
            exit();
        }

        $tempFilePath = STORAGE_DIR . 'temp/' . $tempName;
        if (!file_exists($tempFilePath)) {
            echo json_encode(['success' => false, 'message' => 'File sementara tidak ditemukan. Silakan upload ulang.']);
            exit();
        }

        $fileSize = (float)filesize($tempFilePath);
        $fileSizeMb = round($fileSize / (1024 * 1024), 2);

        // 1. Read Excel
        $readResult = ExcelReader::read($tempFilePath);
        if (!$readResult['success']) {
            echo json_encode(['success' => false, 'message' => $readResult['message']]);
            exit();
        }

        // Store active import state in Session
        $_SESSION['active_import_rows'] = $readResult['rows'];
        $_SESSION['active_import_duplicates'] = $readResult['duplicates'];
        $_SESSION['active_import_file_name'] = $originalName;
        $_SESSION['active_import_temp_name'] = $tempName;
        $_SESSION['active_import_file_size_mb'] = $fileSizeMb;

        // Release session lock to prevent blocking
        session_write_close();

        // Generate and return preview data
        echo json_encode(generatePreviewData());
        exit();
    }

    if ($action === 'update_row') {
        if (!isset($_SESSION['active_import_rows'])) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada session impor yang aktif.']);
            exit();
        }

        $rowNum = (int)($input['row'] ?? 0);
        $patientName = trim($input['patient_name'] ?? '');
        $doctorName = trim($input['doctor_name'] ?? '');
        $tindakan = trim($input['tindakan'] ?? '');
        $tarif = (float)($input['tarif'] ?? 0.0);

        if ($rowNum <= 0) {
            echo json_encode(['success' => false, 'message' => 'Nomor baris tidak valid.']);
            exit();
        }

        // Find and update row in session
        $found = false;
        foreach ($_SESSION['active_import_rows'] as &$row) {
            if ($row['row'] === $rowNum) {
                $originalDoctorName = $row['doctor_name'] ?? '';
                
                $row['patient_name'] = $patientName;
                $row['doctor_name'] = $doctorName;
                $row['tindakan'] = $tindakan;
                $row['tarif'] = $tarif;
                $found = true;

                // Auto save alias if they corrected an unrecognized doctor to an official doctor name
                if ($originalDoctorName !== '' && $doctorName !== '' && strcasecmp($originalDoctorName, $doctorName) !== 0) {
                    Doctor::addAliasByDoctorName($doctorName, $originalDoctorName);
                }
                break;
            }
        }

        if (!$found) {
            echo json_encode(['success' => false, 'message' => "Baris {$rowNum} tidak ditemukan di data impor."]);
            exit();
        }

        // Release session lock to prevent blocking
        session_write_close();

        // Generate and return updated preview data
        echo json_encode(generatePreviewData());
        exit();
    }

    if ($action === 'generate_final') {
        if (!isset($_SESSION['active_import_rows'])) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada session impor yang aktif.']);
            exit();
        }

        $rows = $_SESSION['active_import_rows'];
        $duplicates = $_SESSION['active_import_duplicates'] ?? [];
        $originalName = $_SESSION['active_import_file_name'] ?? 'import.xlsx';
        $tempName = $_SESSION['active_import_temp_name'] ?? '';
        $fileSizeMb = $_SESSION['active_import_file_size_mb'] ?? 0.0;

        $tempFilePath = STORAGE_DIR . 'temp/' . $tempName;

        $startTime = microtime(true);

        // Group and calculate
        $calcService = new CalculationService();
        $groupService = new GroupingService();
        $groupResult = $groupService->group($rows, $calcService);

        try {
            $timestamp = time();
            $safeOriginalName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalName);
            $safeBaseName = pathinfo($safeOriginalName, PATHINFO_FILENAME);
            
            $outputFileName = 'REKAP_JASPEL_' . $timestamp . '_' . $safeBaseName . '.xlsx';
            $outputZipName = 'REKAP_JASPEL_' . $timestamp . '_' . $safeBaseName . '.zip';
            $inputSavedName = 'INPUT_JASPEL_' . $timestamp . '_' . $safeOriginalName;

            // Path locations
            $exportPath = STORAGE_DIR . 'exports/' . $outputFileName;
            $exportZipPath = STORAGE_DIR . 'exports/' . $outputZipName;
            $rawSavedPath = STORAGE_DIR . 'imports/raw/' . $inputSavedName;
            $processedSavedPath = STORAGE_DIR . 'imports/processed/' . $outputFileName;

            // Ensure directories exist
            @mkdir(STORAGE_DIR . 'exports', 0777, true);
            @mkdir(STORAGE_DIR . 'imports/raw', 0777, true);
            @mkdir(STORAGE_DIR . 'imports/processed', 0777, true);

            // Run Export using Service
            $exportRes = ExportService::generate($groupResult['grouped'], $exportPath, $rows);
            if (!$exportRes) {
                throw new RuntimeException("Gagal mengenerate file Excel hasil rekap.");
            }

            // Move original input file from temp to raw storage if exists (synchronous)
            if (file_exists($tempFilePath)) {
                rename($tempFilePath, $rawSavedPath);
            }

            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);

            // Save Import History to DB
            $db = Database::getConnection();
            $db->beginTransaction();

            $stmtHistory = $db->prepare("
                INSERT INTO import_history (
                    file_name, output_file, total_rows, success_rows, failed_rows, 
                    total_departments, total_doctors, total_jaspel, duration_seconds, 
                    file_size_mb, imported_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $totalDepts = count($groupResult['grouped']);
            
            // Count unique doctors in grouped
            $uniqueDoctors = [];
            foreach ($groupResult['grouped'] as $dept => $txs) {
                foreach ($txs as $t) {
                    if (!in_array($t['doctor_name'], $uniqueDoctors)) {
                        $uniqueDoctors[] = $t['doctor_name'];
                    }
                }
            }
            $totalDocs = count($uniqueDoctors);

            $stmtHistory->execute([
                $originalName,
                $outputFileName,
                count($rows) + count($duplicates),
                $groupResult['success_count'],
                $groupResult['failed_count'],
                $totalDepts,
                $totalDocs,
                $groupResult['total_jaspel'],
                $duration,
                $fileSizeMb,
                Session::getUserId()
            ]);

            $historyId = (int)$db->lastInsertId();

            // Save Import Errors to DB (if any remaining ignored)
            if (!empty($groupResult['errors'])) {
                $stmtError = $db->prepare("
                    INSERT INTO import_errors (history_id, row_number, doctor_name, error_message)
                    VALUES (?, ?, ?, ?)
                ");
                foreach ($groupResult['errors'] as $err) {
                    $stmtError->execute([
                        $historyId,
                        $err['row'],
                        $err['doctor_name'],
                        $err['message']
                    ]);
                }
            }

            $db->commit();

            Helper::logActivity('Import Excel', "Sukses menyelesaikan rekapitulasi file: {$originalName} (History ID: {$historyId})");

            $previewData = generatePreviewData(); // Generate full preview data for archiving

            // Clear session temporary import data
            unset($_SESSION['active_import_rows']);
            unset($_SESSION['active_import_duplicates']);
            unset($_SESSION['active_import_file_name']);
            unset($_SESSION['active_import_temp_name']);
            unset($_SESSION['active_import_file_size_mb']);

            $responseData = [
                'success'          => true,
                'history_id'       => $historyId,
                'file_name'        => $originalName,
                'output_file'      => $outputFileName,
                'output_zip'       => $outputZipName,
                'output_doctor_zip'=> 'REKAP_JASPEL_PER_DOKTER_' . $timestamp . '_' . $safeBaseName . '.zip',
                'total_rows'       => count($rows) + count($duplicates),
                'success_rows'     => $groupResult['success_count'],
                'failed_rows'      => $groupResult['failed_count'],
                'total_depts'      => $totalDepts,
                'total_docs'       => $totalDocs,
                'departments'      => array_keys($groupResult['grouped']),
                'total_tarif'      => array_sum(array_map(fn($txs) => array_sum(array_column($txs, 'tarif')), $groupResult['grouped'])),
                'total_jaspel'     => $groupResult['total_jaspel'],
                'formatted_jaspel' => Helper::formatRupiah($groupResult['total_jaspel']),
                'formatted_tarif'  => Helper::formatRupiah(array_sum(array_map(fn($txs) => array_sum(array_column($txs, 'tarif')), $groupResult['grouped']))),
                'duration'         => $duration,
                'file_size'        => $fileSizeMb,
                'preview_data'     => $previewData // Include this so we can save it
            ];

            // Send JSON response immediately, then do background tasks
            $jsonOut = json_encode($responseData);
            header('Content-Length: ' . strlen($jsonOut));
            echo $jsonOut;
            
            // Save json to file for history preview
            $jsonPath = STORAGE_DIR . 'exports/' . str_replace('.xlsx', '.json', $outputFileName);
            file_put_contents($jsonPath, $jsonOut);

            // Flush & close connection so browser gets response immediately
            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            } else {
                ignore_user_abort(true);
                ob_end_flush();
                flush();
            }

            // Background tasks: ZIP + copy to processed (user won't wait for this)
            ExportService::compressToZip($exportPath, $exportZipPath);
            copy($exportPath, $processedSavedPath);
            
            // Also generate per-doctor zip
            $doctorZipName = 'REKAP_JASPEL_PER_DOKTER_' . $timestamp . '_' . $safeBaseName . '.zip';
            $doctorZipPath = STORAGE_DIR . 'exports/' . $doctorZipName;
            ExportDoctorService::generateZipPerDoctor($groupResult['grouped'], $doctorZipPath);

            exit();

        } catch (Exception $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            Helper::logSystemError("Excel final generation failure: " . $e->getMessage(), $e->getTraceAsString());
            echo json_encode(['success' => false, 'message' => 'Gagal merampungkan file Excel: ' . $e->getMessage()]);
            exit();
        }
    } // end generate_final

    if ($action === 'export_single_doctor') {
        $input = json_decode(file_get_contents('php://input'), true);
        $deptName = $input['dept_name'] ?? '';
        $docName = $input['doctor_name'] ?? '';
        $txs = $input['txs'] ?? [];
        
        if (empty($txs)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada data pasien untuk diexport.']);
            exit;
        }
        
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $docName);
        $tempFileName = 'Temp_' . time() . '_' . $safeName . '.xlsx';
        $savePath = STORAGE_DIR . 'exports/' . $tempFileName;
        
        // Cleanup old Temp_ files (older than 1 hour) to save space
        foreach (glob(STORAGE_DIR . 'exports/Temp_*.xlsx') as $tf) {
            if (is_file($tf) && time() - filemtime($tf) > 3600) {
                @unlink($tf);
            }
        }
        
        $success = ExportDoctorService::generateSingleDoctorExcel($txs, $deptName, $docName, $savePath);
        
        if ($success) {
            echo json_encode(['success' => true, 'file' => $tempFileName]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengenerate file Excel.']);
        }
        exit;
    }
}

/**
 * Generate preview data from current active session rows
 */
function generatePreviewData(): array {
    $rows = $_SESSION['active_import_rows'] ?? [];
    $duplicates = $_SESSION['active_import_duplicates'] ?? [];
    $originalName = $_SESSION['active_import_file_name'] ?? 'import.xlsx';
    $tempName = $_SESSION['active_import_temp_name'] ?? '';
    $fileSizeMb = $_SESSION['active_import_file_size_mb'] ?? 0.0;

    // Group and calculate
    $calcService = new CalculationService();
    $groupService = new GroupingService();
    $groupResult = $groupService->group($rows, $calcService);

    // 1. Calculate Department summary
    $rekapDepts = [];
    $totalTarif = 0.0;
    foreach ($groupResult['grouped'] as $deptName => $txs) {
        $deptTarif = array_sum(array_column($txs, 'tarif'));
        $deptJaspel = array_sum(array_column($txs, 'jaspel'));
        $totalTarif += $deptTarif;
        
        $rekapDepts[] = [
            'department_name'  => $deptName,
            'total_tarif'      => $deptTarif,
            'total_jaspel'     => $deptJaspel,
            'formatted_tarif'  => Helper::formatRupiah($deptTarif),
            'formatted_jaspel' => Helper::formatRupiah($deptJaspel)
        ];
    }

    // 2. Calculate Doctor summary
    $rekapDocsRaw = [];
    foreach ($groupResult['grouped'] as $deptName => $txs) {
        foreach ($txs as $t) {
            $docName = $t['doctor_name'];
            $key = $docName . '||' . $deptName;
            if (!isset($rekapDocsRaw[$key])) {
                $rekapDocsRaw[$key] = [
                    'doctor_name'     => $docName,
                    'department_name' => $deptName,
                    'total_patients'  => 0,
                    'total_tarif'     => 0.0,
                    'total_jaspel'    => 0.0
                ];
            }
            $rekapDocsRaw[$key]['total_patients']++;
            $rekapDocsRaw[$key]['total_tarif'] += $t['tarif'];
            $rekapDocsRaw[$key]['total_jaspel'] += $t['jaspel'];
        }
    }

    $rekapDoctors = [];
    foreach ($rekapDocsRaw as $rd) {
        $rd['formatted_tarif'] = Helper::formatRupiah($rd['total_tarif']);
        $rd['formatted_jaspel'] = Helper::formatRupiah($rd['total_jaspel']);
        $rekapDoctors[] = $rd;
    }

    // Format individual transaction values inside grouped
    $formattedGrouped = [];
    foreach ($groupResult['grouped'] as $deptName => $txs) {
        $formattedGrouped[$deptName] = array_map(function($t) {
            $t['formatted_tarif'] = Helper::formatRupiah($t['tarif']);
            $t['formatted_jaspel'] = Helper::formatRupiah($t['jaspel']);
            return $t;
        }, $txs);
    }

    return [
        'success'          => true,
        'has_errors'       => !empty($groupResult['errors']),
        'errors'           => $groupResult['errors'],
        'duplicates_count' => count($duplicates),
        'duplicates'       => $duplicates,
        'total_rows'       => count($rows) + count($duplicates),
        'success_rows'     => $groupResult['success_count'],
        'failed_rows'      => $groupResult['failed_count'],
        'temp_name'        => $tempName,
        'file_name'        => $originalName,
        'file_size'        => $fileSizeMb,
        'total_tarif'      => $totalTarif,
        'total_jaspel'     => $groupResult['total_jaspel'],
        'formatted_tarif'  => Helper::formatRupiah($totalTarif),
        'formatted_jaspel' => Helper::formatRupiah($groupResult['total_jaspel']),
        'rekap_depts'      => $rekapDepts,
        'rekap_doctors'    => $rekapDoctors,
        'grouped'          => $formattedGrouped,
        'departments'      => array_keys($formattedGrouped)
    ];
}

header('HTTP/1.1 400 Bad Request');
echo json_encode(['success' => false, 'message' => 'Invalid request method']);
exit();
