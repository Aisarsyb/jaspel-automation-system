<?php
declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportService {
    /**
     * Generate the formatted Excel workbook matching reference template format.
     */
    public static function generate(array $groupedData, string $savePath, array $rawRows = []): bool {
        try {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->removeSheetByIndex(0); // Remove default sheet

            $currencyFormat = '_-[$Rp-421]* #,##0_-;\-[$Rp-421]* #,##0_-;_-[$Rp-421]* "-"_-;_-@_-';

            $db = Database::getConnection();

            // Department mapping configuration
            $deptConfig = [
                'Bedah Mulut dan Maksilofasial' => ['sheet' => 'BEDAH MULUT', 'label' => 'BM'],
                'Periodonsia'                   => ['sheet' => 'PERIODONSIA', 'label' => 'Periodonsia'],
                'Konservasi'                    => ['sheet' => 'KONSERVASI', 'label' => 'Konservasi'],
                'IKGA'                          => ['sheet' => 'IKGA', 'label' => 'IKGA'],
                'IPM'                           => ['sheet' => 'IPM', 'label' => 'IPM'],
                'Prostodonsia'                  => ['sheet' => 'PROSTODONSIA', 'label' => 'Prostodonsia'],
                'ORGANIK RSGM'                  => ['sheet' => 'ORGANIK RSGM', 'label' => 'Organik RSGM']
            ];

            // 1. Raw Input Sheet (IRJA)
            if (!empty($rawRows)) {
                $rawSheet = $spreadsheet->createSheet();
                $rawSheet->setTitle('IRJA');
                $rawSheet->setShowGridlines(true);

                $headers = ['ADMISSION_DATE', 'NAMA_PASIEN', 'DESKRIPSI_INACBG', 'TOTAL_TARIF', 'RADIOLOGI', 'DPJP'];
                foreach ($headers as $colIdx => $hText) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                    $rawSheet->setCellValue("{$colLetter}1", $hText);
                }
                $rawSheet->getStyle('A1:F1')->getFont()->setBold(true);

                $rRow = 2;
                foreach ($rawRows as $r) {
                    $rawSheet->setCellValue("A{$rRow}", $r['tanggal'] ?? '');
                    $rawSheet->setCellValue("B{$rRow}", $r['patient_name'] ?? '');
                    $rawSheet->setCellValue("C{$rRow}", $r['tindakan'] ?? '');
                    $rawSheet->setCellValue("D{$rRow}", $r['tarif'] ?? 0);
                    $rawSheet->setCellValue("E{$rRow}", $r['radiologi'] ?? 0);
                    $rawSheet->setCellValue("F{$rRow}", $r['doctor_name'] ?? '');
                    $rRow++;
                }
            }

            // Fetch active departments from DB
            $stmtDepts = $db->query("SELECT id, department_name FROM departments WHERE status = 'active' ORDER BY id ASC");
            $dbDepts = $stmtDepts->fetchAll(PDO::FETCH_ASSOC);

            // Store references for Rekap sheet: doctorOfficialName => ['sheet' => sheetName, 'row' => totalRowIndex]
            $doctorTotalCellMap = [];
            $deptTotalCells = []; // deptName => rekapTotalCell

            // 2. Build Department Sheets
            foreach ($dbDepts as $d) {
                $deptName = $d['department_name'];
                $deptId   = (int)$d['id'];
                
                $isRkg = (stripos($deptName, 'Radiologi') !== false);

                if ($isRkg) {
                    $cfg = ['sheet' => 'RADIOLOGI', 'label' => 'RKG'];
                } else {
                    $cfg = $deptConfig[$deptName] ?? [
                        'sheet' => strtoupper(substr(trim($deptName), 0, 30)),
                        'label' => trim($deptName)
                    ];
                }
                
                $sheetName = $cfg['sheet'];
                $deptLabel = $cfg['label'];

                $sheet = $spreadsheet->createSheet();
                $sheet->setTitle($sheetName);
                $sheet->setShowGridlines(true);

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(9);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(32);
                $sheet->getColumnDimension('D')->setWidth(35);
                $sheet->getColumnDimension('E')->setWidth(17);
                $sheet->getColumnDimension('F')->setWidth(17);

                // Sheet title
                $sheet->setCellValue('A1', 'JASA MEDIS PASIEN BPJS RAWAT JALAN');
                $sheet->getStyle('A1')->getFont()->setName('Calibri')->setSize(11)->setBold(true);

                $blueFill = [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'BDD7EE']
                ];

                $isRkg = (stripos($deptName, 'Radiologi') !== false);

                if ($isRkg) {
                    // --- RADIOLOGI SPECIAL SHEET ---
                    $sheet->setCellValue('A3', 'Nama DPJP ');
                    $sheet->setCellValue('C3', ':');
                    $sheet->setCellValue('A4', 'Departemen');
                    $sheet->setCellValue('C4', ': RKG');

                    // Headers at A6:F6
                    $sheet->setCellValue('A6', 'No');
                    $sheet->setCellValue('B6', 'Tanggal');
                    $sheet->setCellValue('C6', 'Nama Pasien');
                    $sheet->setCellValue('D6', 'Tindakan');
                    $sheet->setCellValue('E6', 'Tarif ');
                    $sheet->setCellValue('F6', 'Jaspel');
                    $sheet->getStyle('A6:F6')->getFont()->setName('Calibri')->setBold(true);
                    $sheet->getStyle('A6:F6')->getFill()->applyFromArray($blueFill);
                    $sheet->getStyle('A6:B6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E6:F6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    $rkgTxs = $groupedData[$deptName] ?? [];
                    if (empty($rkgTxs) && isset($groupedData['Radiologi Kedokteran Gigi'])) {
                        $rkgTxs = $groupedData['Radiologi Kedokteran Gigi'];
                    }

                    $curRow = 7;

                    if (!empty($rkgTxs)) {
                        foreach ($rkgTxs as $idx => $t) {
                            $sheet->setCellValue("A{$curRow}", $idx + 1);
                            $sheet->setCellValue("B{$curRow}", $t['tanggal'] ?? '');
                            $sheet->setCellValue("C{$curRow}", $t['patient_name'] ?? '');
                            $sheet->setCellValue("D{$curRow}", $t['tindakan'] ?? '');
                            $sheet->setCellValue("E{$curRow}", $t['tarif'] ?? 0);
                            $sheet->setCellValue("F{$curRow}", "=" . RKG_JASPEL_PERCENTAGE . "%*E{$curRow}");

                            $sheet->getStyle("A{$curRow}:B{$curRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle("E{$curRow}:F{$curRow}")->getNumberFormat()->setFormatCode($currencyFormat);
                            $curRow++;
                        }
                        $dataEnd = $curRow - 1;
                    } else {
                        $sheet->setCellValue("A7", 1);
                        $sheet->setCellValue("E7", 0);
                        $sheet->setCellValue("F7", "=" . RKG_JASPEL_PERCENTAGE . "%*E7");
                        $sheet->getStyle("E7:F7")->getNumberFormat()->setFormatCode($currencyFormat);
                        $dataEnd = 7;
                        $curRow = 8;
                    }

                    // Total Row
                    $sheet->setCellValue("D{$curRow}", 'TOTAL ');
                    $sheet->setCellValue("E{$curRow}", "=SUM(E7:E{$dataEnd})");
                    $sheet->setCellValue("F{$curRow}", "=SUM(F7:F{$dataEnd})");
                    $sheet->getStyle("D{$curRow}:F{$curRow}")->getFont()->setBold(true);
                    $sheet->getStyle("E{$curRow}:F{$curRow}")->getNumberFormat()->setFormatCode($currencyFormat);
                    $sheet->getStyle("D{$curRow}:F{$curRow}")->getFill()->applyFromArray($blueFill);

                    $taxRow = $curRow + 1;
                    $sheet->setCellValue("D{$taxRow}", 'PAJAK');
                    $sheet->getStyle("D{$taxRow}:F{$taxRow}")->getFont()->setBold(true);
                    $sheet->getStyle("D{$taxRow}:F{$taxRow}")->getFill()->applyFromArray($blueFill);

                    $afterTaxRow = $curRow + 2;
                    $sheet->setCellValue("D{$afterTaxRow}", 'TOTAL (setelah pajak)');
                    $sheet->getStyle("D{$afterTaxRow}:F{$afterTaxRow}")->getFont()->setBold(true);
                    $sheet->getStyle("D{$afterTaxRow}:F{$afterTaxRow}")->getFill()->applyFromArray($blueFill);

                    $doctorTotalCellMap['DPJP RKG'] = [
                        'sheet' => $sheetName,
                        'row'   => $curRow,
                        'is_rkg' => true
                    ];

                } else {
                    // --- STANDARD DEPARTMENT SHEETS ---
                    $stmtDocs = $db->prepare("SELECT doctor_name FROM dpjp WHERE department_id = ? ORDER BY id ASC");
                    $stmtDocs->execute([$deptId]);
                    $deptDoctors = $stmtDocs->fetchAll(PDO::FETCH_COLUMN);

                    $deptTxs = $groupedData[$deptName] ?? [];

                    // Group transactions by official doctor name
                    $txsByDoctor = [];
                    foreach ($deptTxs as $t) {
                        $docName = $t['doctor_name'];
                        if (!isset($txsByDoctor[$docName])) {
                            $txsByDoctor[$docName] = [];
                        }
                        $txsByDoctor[$docName][] = $t;
                    }

                    $curRow = 3;
                    foreach ($deptDoctors as $officialName) {
                        $sheet->setCellValue("A{$curRow}", 'Nama DPJP ');
                        $sheet->setCellValue("C{$curRow}", ': ' . strtoupper($officialName));
                        $sheet->setCellValue("A" . ($curRow + 1), 'Departemen');
                        $sheet->setCellValue("C" . ($curRow + 1), ': ' . $deptLabel);

                        // Headers
                        $hRow = $curRow + 3;
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

                        $docTxs = $txsByDoctor[$officialName] ?? [];
                        $dataStart = $hRow + 1;

                        if (!empty($docTxs)) {
                            $dRow = $dataStart;
                            foreach ($docTxs as $idx => $t) {
                                $sheet->setCellValue("A{$dRow}", $idx + 1);
                                $sheet->setCellValue("B{$dRow}", $t['tanggal'] ?? '');
                                $sheet->setCellValue("C{$dRow}", $t['patient_name'] ?? '');
                                $sheet->setCellValue("D{$dRow}", $t['tindakan'] ?? '');
                                $sheet->setCellValue("E{$dRow}", $t['tarif'] ?? 0);
                                $sheet->setCellValue("F{$dRow}", "=" . JASPEL_PERCENTAGE . "%*E{$dRow}");

                                $sheet->getStyle("A{$dRow}:B{$dRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                                $sheet->getStyle("E{$dRow}:F{$dRow}")->getNumberFormat()->setFormatCode($currencyFormat);
                                $dRow++;
                            }
                            $dataEnd = $dRow - 1;
                        } else {
                            $dRow = $dataStart;
                            $sheet->setCellValue("E{$dRow}", 0);
                            $sheet->setCellValue("F{$dRow}", "=" . JASPEL_PERCENTAGE . "%*E{$dRow}");
                            $sheet->getStyle("E{$dRow}:F{$dRow}")->getNumberFormat()->setFormatCode($currencyFormat);
                            $dataEnd = $dRow;
                            $dRow++;
                        }

                        // Summary rows
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

                        $doctorTotalCellMap[$officialName] = [
                            'sheet' => $sheetName,
                            'row'   => $totRow,
                            'is_rkg' => false
                        ];

                        $curRow = $afterTaxRow + 3; // Space before next doctor
                    }
                }
            }

            // 3. Rekap Sheet
            $rekapSheet = $spreadsheet->createSheet();
            $rekapSheet->setTitle('Rekap');
            $rekapSheet->setShowGridlines(true);

            $rekapSheet->getColumnDimension('A')->setWidth(7);
            $rekapSheet->getColumnDimension('B')->setWidth(72);
            $rekapSheet->getColumnDimension('C')->setWidth(22);
            $rekapSheet->getColumnDimension('D')->setWidth(20);

            $rekapSheet->setCellValue('A1', 'Rekap Jasa Pelayanan Instalasi Rawat Jalan');
            $rekapSheet->setCellValue('A2', 'Bulan : -');
            $rekapSheet->getStyle('A1:A2')->getFont()->setName('Calibri')->setSize(12);

            $rRow = 4;
            $deptSumCells = [];

            foreach ($dbDepts as $d) {
                $deptName = $d['department_name'];
                $deptId   = (int)$d['id'];
                $isRkg    = (stripos($deptName, 'Radiologi') !== false);

                $rekapSheet->setCellValue("A{$rRow}", "Departemen      : {$deptName}");
                $rekapSheet->getStyle("A{$rRow}")->getFont()->setName('Calibri')->setSize(12);

                $hRow = $rRow + 2;
                $rekapSheet->setCellValue("A{$hRow}", 'No');
                $rekapSheet->setCellValue("B{$hRow}", 'Nama DPJP');
                $rekapSheet->setCellValue("C{$hRow}", $isRkg ? 'Nominal' : 'Tarif Total');
                $rekapSheet->setCellValue("D{$hRow}", 'Jaspel');
                $rekapSheet->getStyle("A{$hRow}:D{$hRow}")->getFont()->setName('Calibri')->setBold(true);
                $rekapSheet->getStyle("A{$hRow}:D{$hRow}")->getFill()->applyFromArray($blueFill);
                $rekapSheet->getStyle("A{$hRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $rekapSheet->getStyle("C{$hRow}:D{$hRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                if ($isRkg) {
                    $docList = ['DPJP RKG'];
                } else {
                    $stmtDocs = $db->prepare("SELECT doctor_name FROM dpjp WHERE department_id = ? ORDER BY id ASC");
                    $stmtDocs->execute([$deptId]);
                    $docList = $stmtDocs->fetchAll(PDO::FETCH_COLUMN);
                }

                $dataStart = $hRow + 1;
                $curD = $dataStart;

                foreach ($docList as $idx => $docName) {
                    $rekapSheet->setCellValue("A{$curD}", $idx + 1);
                    $rekapSheet->setCellValue("B{$curD}", $docName);

                    if (isset($doctorTotalCellMap[$docName])) {
                        $ref = $doctorTotalCellMap[$docName];
                        $refSheet = strpos($ref['sheet'], ' ') !== false ? "'{$ref['sheet']}'" : $ref['sheet'];
                        $rekapSheet->setCellValue("C{$curD}", "={$refSheet}!E{$ref['row']}");

                        if ($ref['is_rkg']) {
                            $rekapSheet->setCellValue("D{$curD}", "=C{$curD}*" . RKG_JASPEL_PERCENTAGE . "%");
                        } else {
                            $rekapSheet->setCellValue("D{$curD}", "=C{$curD}*" . JASPEL_PERCENTAGE . "%");
                        }
                    } else {
                        $rekapSheet->setCellValue("C{$curD}", 0);
                        $rekapSheet->setCellValue("D{$curD}", "=C{$curD}*" . JASPEL_PERCENTAGE . "%");
                    }

                    $rekapSheet->getStyle("A{$curD}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $rekapSheet->getStyle("C{$curD}:D{$curD}")->getNumberFormat()->setFormatCode($currencyFormat);
                    $curD++;
                }

                $deptTotRow = $curD;
                $rekapSheet->setCellValue("C{$deptTotRow}", "=SUM(C{$dataStart}:C" . ($deptTotRow - 1) . ")");
                $rekapSheet->setCellValue("D{$deptTotRow}", "=SUM(D{$dataStart}:D" . ($deptTotRow - 1) . ")");
                $rekapSheet->getStyle("C{$deptTotRow}:D{$deptTotRow}")->getFont()->setBold(true);
                $rekapSheet->getStyle("C{$deptTotRow}:D{$deptTotRow}")->getNumberFormat()->setFormatCode($currencyFormat);

                $deptSumCells[] = "C{$deptTotRow}";

                $rRow = $deptTotRow + 3; // Space before next dept
            }

            // Bottom Grand Total Block
            $grandTotalRow = $rRow + 1;
            if (!empty($deptSumCells)) {
                $rekapSheet->setCellValue("C{$grandTotalRow}", "=" . implode('+', $deptSumCells));
                $rekapSheet->getStyle("C{$grandTotalRow}")->getFont()->setBold(true);
                $rekapSheet->getStyle("C{$grandTotalRow}")->getNumberFormat()->setFormatCode($currencyFormat);
            }

            // Save Workbook
            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->save($savePath);

            return true;
        } catch (Exception $e) {
            Helper::logSystemError("Export Excel creation failed: " . $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Compress the Excel output file into a ZIP archive.
     */
    public static function compressToZip(string $excelFilePath, string $zipSavePath): bool {
        if (!file_exists($excelFilePath)) {
            return false;
        }

        try {
            $zip = new ZipArchive();
            if ($zip->open($zipSavePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                $baseName = basename($excelFilePath);
                $zip->addFile($excelFilePath, $baseName);
                $zip->close();
                return true;
            }
        } catch (Exception $e) {
            Helper::logSystemError("ZIP creation failed: " . $e->getMessage(), $e->getTraceAsString());
        }
        return false;
    }
}
