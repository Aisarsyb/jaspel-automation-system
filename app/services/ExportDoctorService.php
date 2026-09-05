<?php
declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportDoctorService {
    public static function generateZipPerDoctor(array $groupedData, string $zipSavePath): bool {
        try {
            $currencyFormat = '_-[$Rp-421]* #,##0_-;\-[$Rp-421]* #,##0_-;_-[$Rp-421]* "-"_-;_-@_-';
            $blueFill = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'BDD7EE']];
            $redFill  = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFC7CE']];

            $db = Database::getConnection();
            $stmtDepts = $db->query("SELECT id, department_name FROM departments WHERE status = 'active'");
            $dbDepts = $stmtDepts->fetchAll(PDO::FETCH_ASSOC);

            $tempDir = dirname($zipSavePath) . '/temp_doctors_' . time();
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0777, true);
            }

            $zip = new ZipArchive();
            if ($zip->open($zipSavePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                return false;
            }

            foreach ($dbDepts as $d) {
                $deptName = $d['department_name'];
                $deptId   = (int)$d['id'];
                $isRkg    = (stripos($deptName, 'Radiologi') !== false);

                $deptTxs = $groupedData[$deptName] ?? [];
                if ($isRkg && empty($deptTxs) && isset($groupedData['Radiologi Kedokteran Gigi'])) {
                    $deptTxs = $groupedData['Radiologi Kedokteran Gigi'];
                }

                $txsByDoctor = [];
                foreach ($deptTxs as $t) {
                    $docName = $t['doctor_name'];
                    if (!isset($txsByDoctor[$docName])) {
                        $txsByDoctor[$docName] = [];
                    }
                    $txsByDoctor[$docName][] = $t;
                }

                if ($isRkg) {
                    $dbDocs = ['DPJP RKG'];
                } else {
                    $stmtDocs = $db->prepare("SELECT doctor_name FROM dpjp WHERE department_id = ?");
                    $stmtDocs->execute([$deptId]);
                    $dbDocs = $stmtDocs->fetchAll(PDO::FETCH_COLUMN);
                }

                foreach ($dbDocs as $officialName) {
                    $docTxs = $txsByDoctor[$officialName] ?? [];
                    if (empty($docTxs)) continue;

                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->setTitle('Jasa Medis');
                    $sheet->setShowGridlines(true);

                    $sheet->getColumnDimension('A')->setWidth(9);
                    $sheet->getColumnDimension('B')->setWidth(12);
                    $sheet->getColumnDimension('C')->setWidth(32);
                    $sheet->getColumnDimension('D')->setWidth(35);
                    $sheet->getColumnDimension('E')->setWidth(17);
                    $sheet->getColumnDimension('F')->setWidth(17);

                    $sheet->setCellValue('A1', 'JASA MEDIS PASIEN BPJS RAWAT JALAN');
                    $sheet->getStyle('A1')->getFont()->setName('Calibri')->setSize(11)->setBold(true);

                    $sheet->setCellValue('A3', 'Nama DPJP ');
                    $sheet->setCellValue('C3', ': ' . strtoupper($officialName));
                    $sheet->setCellValue('A4', 'Departemen');
                    $sheet->setCellValue('C4', ': ' . $deptName);

                    $hRow = 6;
                    $sheet->setCellValue("A{$hRow}", 'No');
                    $sheet->setCellValue("B{$hRow}", 'Tanggal');
                    $sheet->setCellValue("C{$hRow}", 'Nama Pasien');
                    $sheet->setCellValue("D{$hRow}", 'Tindakan');
                    $sheet->setCellValue("E{$hRow}", 'Tarif ');
                    $sheet->setCellValue("F{$hRow}", 'Jaspel');
                    $sheet->getStyle("A{$hRow}:F{$hRow}")->getFont()->setName('Calibri')->setBold(true);
                    $sheet->getStyle("A{$hRow}:F{$hRow}")->getFill()->applyFromArray($blueFill);
                    $sheet->getStyle("A{$hRow}:B{$hRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("E{$hRow}:F{$hRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    $dRow = $hRow + 1;
                    $dataStart = $dRow;

                    $percentage = $isRkg ? RKG_JASPEL_PERCENTAGE : JASPEL_PERCENTAGE;

                    // Normal rows first
                    $normalTxs = array_values(array_filter($docTxs, fn($t) => empty($t['is_tlb'])));
                    $tlbTxs    = array_values(array_filter($docTxs, fn($t) => !empty($t['is_tlb'])));
                    $rowNo = 1;

                    foreach ($normalTxs as $t) {
                        $sheet->setCellValue("A{$dRow}", $rowNo++);
                        $sheet->setCellValue("B{$dRow}", $t['tanggal'] ?? '');
                        $sheet->setCellValue("C{$dRow}", $t['patient_name'] ?? '');
                        $sheet->setCellValue("D{$dRow}", $t['tindakan'] ?? '');
                        $sheet->setCellValue("E{$dRow}", $t['tarif'] ?? 0);
                        $sheet->setCellValue("F{$dRow}", "=" . $percentage . "%*E{$dRow}");
                        $sheet->getStyle("A{$dRow}:B{$dRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("E{$dRow}:F{$dRow}")->getNumberFormat()->setFormatCode($currencyFormat);
                        $dRow++;
                    }

                    $normalDataEnd = $dRow - 1;

                    // TLB rows below (red, display only)
                    foreach ($tlbTxs as $t) {
                        $sheet->setCellValue("A{$dRow}", $rowNo++);
                        $sheet->setCellValue("B{$dRow}", $t['tanggal'] ?? '');
                        $sheet->setCellValue("C{$dRow}", $t['patient_name'] ?? '');
                        $sheet->setCellValue("D{$dRow}", $t['tindakan'] ?? '');
                        $sheet->setCellValue("E{$dRow}", $t['tarif'] ?? 0);
                        $sheet->setCellValue("F{$dRow}", "=" . $percentage . "%*E{$dRow}");
                        $sheet->getStyle("A{$dRow}:B{$dRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("E{$dRow}:F{$dRow}")->getNumberFormat()->setFormatCode($currencyFormat);
                        $sheet->getStyle("A{$dRow}:F{$dRow}")->getFill()->applyFromArray($redFill);
                        $dRow++;
                    }

                    $dataEnd = $dRow - 1;
                    $totRow = $dRow;
                    $sheet->setCellValue("D{$totRow}", 'TOTAL ');
                    // TOTAL sums only normal (non-TLB) rows
                    if ($normalDataEnd >= $dataStart) {
                        $sheet->setCellValue("E{$totRow}", "=SUM(E{$dataStart}:E{$normalDataEnd})");
                        $sheet->setCellValue("F{$totRow}", "=SUM(F{$dataStart}:F{$normalDataEnd})");
                    } else {
                        $sheet->setCellValue("E{$totRow}", 0);
                        $sheet->setCellValue("F{$totRow}", 0);
                    }
                    $sheet->getStyle("D{$totRow}:F{$totRow}")->getFont()->setBold(true);
                    $sheet->getStyle("E{$totRow}:F{$totRow}")->getNumberFormat()->setFormatCode($currencyFormat);
                    $sheet->getStyle("D{$totRow}:F{$totRow}")->getFill()->applyFromArray($blueFill);

                    $taxRow = $totRow + 1;
                    $sheet->setCellValue("D{$taxRow}", 'PAJAK');
                    $sheet->getStyle("D{$taxRow}:F{$taxRow}")->getFont()->setBold(true);
                    $sheet->getStyle("D{$taxRow}:F{$taxRow}")->getFill()->applyFromArray($blueFill);

                    $afterTaxRow = $totRow + 2;
                    $sheet->setCellValue("D{$afterTaxRow}", 'TOTAL (setelah pajak)');
                    $sheet->getStyle("D{$afterTaxRow}:F{$afterTaxRow}")->getFont()->setBold(true);
                    $sheet->getStyle("D{$afterTaxRow}:F{$afterTaxRow}")->getFill()->applyFromArray($blueFill);

                    $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $officialName);
                    $docFilePath = $tempDir . "/Jaspel_" . $safeName . ".xlsx";
                    
                    $writer = new Xlsx($spreadsheet);
                    $writer->setPreCalculateFormulas(false);
                    $writer->save($docFilePath);

                    $zip->addFile($docFilePath, basename($docFilePath));
                }
            }

            $zip->close();

            // Cleanup temp dir
            $files = glob($tempDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) unlink($file);
            }
            rmdir($tempDir);

            return true;
        } catch (Exception $e) {
            Helper::logSystemError("Zip Per Doctor failed: " . $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }
    
    public static function generateSingleDoctorExcel(array $docTxs, string $deptName, string $officialName, string $savePath): bool {
        try {
            $currencyFormat = '_-[$Rp-421]* #,##0_-;\-[$Rp-421]* #,##0_-;_-[$Rp-421]* "-"_-;_-@_-';
            $blueFill = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'BDD7EE']];
            $redFill  = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFC7CE']];
            $isRkg    = (stripos($deptName, 'Radiologi') !== false);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Jasa Medis');
            $sheet->setShowGridlines(true);

            $sheet->getColumnDimension('A')->setWidth(9);
            $sheet->getColumnDimension('B')->setWidth(12);
            $sheet->getColumnDimension('C')->setWidth(32);
            $sheet->getColumnDimension('D')->setWidth(35);
            $sheet->getColumnDimension('E')->setWidth(17);
            $sheet->getColumnDimension('F')->setWidth(17);

            $sheet->setCellValue('A1', 'JASA MEDIS PASIEN BPJS RAWAT JALAN');
            $sheet->getStyle('A1')->getFont()->setName('Calibri')->setSize(11)->setBold(true);

            $sheet->setCellValue('A3', 'Nama DPJP ');
            $sheet->setCellValue('C3', ': ' . strtoupper($officialName));
            $sheet->setCellValue('A4', 'Departemen');
            $sheet->setCellValue('C4', ': ' . $deptName);

            $hRow = 6;
            $sheet->setCellValue("A{$hRow}", 'No');
            $sheet->setCellValue("B{$hRow}", 'Tanggal');
            $sheet->setCellValue("C{$hRow}", 'Nama Pasien');
            $sheet->setCellValue("D{$hRow}", 'Tindakan');
            $sheet->setCellValue("E{$hRow}", 'Tarif ');
            $sheet->setCellValue("F{$hRow}", 'Jaspel');
            $sheet->getStyle("A{$hRow}:F{$hRow}")->getFont()->setName('Calibri')->setBold(true);
            $sheet->getStyle("A{$hRow}:F{$hRow}")->getFill()->applyFromArray($blueFill);
            $sheet->getStyle("A{$hRow}:B{$hRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$hRow}:F{$hRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $dRow = $hRow + 1;
            $dataStart = $dRow;

            $percentage = $isRkg ? RKG_JASPEL_PERCENTAGE : JASPEL_PERCENTAGE;

            foreach ($docTxs as $idx => $t) {
                $sheet->setCellValue("A{$dRow}", $idx + 1);
                $sheet->setCellValue("B{$dRow}", $t['tanggal'] ?? '');
                $sheet->setCellValue("C{$dRow}", $t['patient_name'] ?? '');
                $sheet->setCellValue("D{$dRow}", $t['tindakan'] ?? '');
                $sheet->setCellValue("E{$dRow}", $t['tarif'] ?? 0);
                $sheet->setCellValue("F{$dRow}", "=" . $percentage . "%*E{$dRow}");

                $sheet->getStyle("A{$dRow}:B{$dRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$dRow}:F{$dRow}")->getNumberFormat()->setFormatCode($currencyFormat);
                
                if (!empty($t['is_tlb'])) {
                    $sheet->getStyle("A{$dRow}:F{$dRow}")->getFill()->applyFromArray($redFill);
                }
                
                $dRow++;
            }

            $dataEnd = $dRow - 1;
            $totRow = $dRow;
            $sheet->setCellValue("D{$totRow}", 'TOTAL ');
            $sheet->setCellValue("E{$totRow}", "=SUM(E{$dataStart}:E{$dataEnd})");
            $sheet->setCellValue("F{$totRow}", "=SUM(F{$dataStart}:F{$dataEnd})");
            $sheet->getStyle("D{$totRow}:F{$totRow}")->getFont()->setBold(true);
            $sheet->getStyle("E{$totRow}:F{$totRow}")->getNumberFormat()->setFormatCode($currencyFormat);
            $sheet->getStyle("D{$totRow}:F{$totRow}")->getFill()->applyFromArray($blueFill);

            $taxRow = $totRow + 1;
            $sheet->setCellValue("D{$taxRow}", 'PAJAK');
            $sheet->getStyle("D{$taxRow}:F{$taxRow}")->getFont()->setBold(true);
            $sheet->getStyle("D{$taxRow}:F{$taxRow}")->getFill()->applyFromArray($blueFill);

            $afterTaxRow = $totRow + 2;
            $sheet->setCellValue("D{$afterTaxRow}", 'TOTAL (setelah pajak)');
            $sheet->getStyle("D{$afterTaxRow}:F{$afterTaxRow}")->getFont()->setBold(true);
            $sheet->getStyle("D{$afterTaxRow}:F{$afterTaxRow}")->getFill()->applyFromArray($blueFill);

            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            
            $dir = dirname($savePath);
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            
            $writer->save($savePath);
            return true;
        } catch (Exception $e) {
            Helper::logSystemError("Single Doctor Excel failed: " . $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }
}
