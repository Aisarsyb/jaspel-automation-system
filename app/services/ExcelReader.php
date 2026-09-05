<?php
declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelReader {
    /**
     * Read the Excel file and return metadata and parsed rows.
     */
    public static function read(string $filePath): array {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("File tidak ditemukan di path: " . $filePath);
        }

        try {
            // Load spreadsheet
            $spreadsheet = IOFactory::load($filePath);
            
            // Find sheet that contains the raw data headers (Patient, Doctor, Tarif)
            $sheet = null;
            $headerRow = 1;
            $headers = [];
            $colMapping = [
                'patient'   => -1,
                'doctor'    => -1,
                'tarif'     => -1,
                'tindakan'  => -1,
                'radiologi' => -1,
                'tanggal'   => -1
            ];

            foreach ($spreadsheet->getAllSheets() as $candidateSheet) {
                $cMaxRow = $candidateSheet->getHighestRow();
                $cMaxCol = $candidateSheet->getHighestColumn();
                $cMaxColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($cMaxCol);

                for ($row = 1; $row <= min(10, $cMaxRow); $row++) {
                    $testMapping = [
                        'patient'   => -1,
                        'doctor'    => -1,
                        'tarif'     => -1,
                        'tindakan'  => -1,
                        'radiologi' => -1,
                        'tanggal'   => -1
                    ];

                    $rowHeaders = [];
                    for ($col = 1; $col <= $cMaxColIndex; $col++) {
                        $val = trim((string)$candidateSheet->getCell([$col, $row])->getValue());
                        if ($val !== '') {
                            $rowHeaders[$col] = strtolower($val);
                        }
                    }

                    // 1. Patient Mapping
                    foreach ($rowHeaders as $colIdx => $headerVal) {
                        if ($headerVal === 'nama_pasien' || $headerVal === 'nama pasien') {
                            $testMapping['patient'] = $colIdx;
                            break;
                        }
                    }
                    if ($testMapping['patient'] === -1) {
                        foreach ($rowHeaders as $colIdx => $headerVal) {
                            if (strpos($headerVal, 'pasien') !== false || strpos($headerVal, 'patient') !== false) {
                                $testMapping['patient'] = $colIdx;
                                break;
                            }
                        }
                    }

                    // 2. Doctor Mapping
                    foreach ($rowHeaders as $colIdx => $headerVal) {
                        if ($headerVal === 'dpjp' || $headerVal === 'nama_dpjp' || $headerVal === 'nama dpjp') {
                            $testMapping['doctor'] = $colIdx;
                            break;
                        }
                    }
                    if ($testMapping['doctor'] === -1) {
                        foreach ($rowHeaders as $colIdx => $headerVal) {
                            if (strpos($headerVal, 'dpjp') !== false || strpos($headerVal, 'dokter') !== false || strpos($headerVal, 'doctor') !== false) {
                                $testMapping['doctor'] = $colIdx;
                                break;
                            }
                        }
                    }

                    // 3. Tarif Mapping
                    foreach ($rowHeaders as $colIdx => $headerVal) {
                        if ($headerVal === 'total_tarif' || $headerVal === 'total tarif') {
                            $testMapping['tarif'] = $colIdx;
                            break;
                        }
                    }
                    if ($testMapping['tarif'] === -1) {
                        foreach ($rowHeaders as $colIdx => $headerVal) {
                            if ($headerVal === 'tarif' || $headerVal === 'biaya' || $headerVal === 'total') {
                                $testMapping['tarif'] = $colIdx;
                                break;
                            }
                        }
                    }
                    if ($testMapping['tarif'] === -1) {
                        foreach ($rowHeaders as $colIdx => $headerVal) {
                            if (strpos($headerVal, 'tarif') !== false || strpos($headerVal, 'biaya') !== false || strpos($headerVal, 'total') !== false) {
                                $testMapping['tarif'] = $colIdx;
                                break;
                            }
                        }
                    }

                    // 4. Tindakan Mapping
                    foreach ($rowHeaders as $colIdx => $headerVal) {
                        if ($headerVal === 'deskripsi_inacbg' || $headerVal === 'deskripsi inacbg') {
                            $testMapping['tindakan'] = $colIdx;
                            break;
                        }
                    }
                    if ($testMapping['tindakan'] === -1) {
                        foreach ($rowHeaders as $colIdx => $headerVal) {
                            if (strpos($headerVal, 'tindakan') !== false || strpos($headerVal, 'inacbg') !== false || strpos($headerVal, 'deskripsi') !== false || strpos($headerVal, 'procedure') !== false) {
                                $testMapping['tindakan'] = $colIdx;
                                break;
                            }
                        }
                    }

                    // 5. Radiologi Mapping
                    if ($testMapping['radiologi'] === -1) {
                        foreach ($rowHeaders as $colIdx => $headerVal) {
                            if (strpos($headerVal, 'radiologi') !== false || strpos($headerVal, 'radiology') !== false) {
                                $testMapping['radiologi'] = $colIdx;
                                break;
                            }
                        }
                    }

                    // 6. Tanggal Mapping
                    if ($testMapping['tanggal'] === -1) {
                        foreach ($rowHeaders as $colIdx => $headerVal) {
                            if ($headerVal === 'admission_date' || $headerVal === 'admission date' || strpos($headerVal, 'tanggal') !== false || strpos($headerVal, 'date') !== false) {
                                $testMapping['tanggal'] = $colIdx;
                                break;
                            }
                        }
                    }

                    if ($testMapping['patient'] !== -1 && $testMapping['doctor'] !== -1 && $testMapping['tarif'] !== -1) {
                        $sheet = $candidateSheet;
                        $headerRow = $row;
                        $colMapping = $testMapping;
                        foreach ($rowHeaders as $colIdx => $headerVal) {
                            $headers[$colIdx] = trim((string)$sheet->getCell([$colIdx, $row])->getValue());
                        }
                        break 2;
                    }
                }
            }

            if ($sheet === null || $colMapping['patient'] === -1 || $colMapping['doctor'] === -1 || $colMapping['tarif'] === -1) {
                throw new RuntimeException("Format kolom template Excel tidak sesuai. Pastikan kolom Nama Pasien, DPJP/Dokter, dan Tarif tersedia.");
            }

            $maxRow = $sheet->getHighestRow();

            // 2. Read Data Rows
            $rows = [];
            $duplicates = [];
            $seenRows = [];

            for ($row = $headerRow + 1; $row <= $maxRow; $row++) {
                // Handle rows highlighted in red (TLB)
                $isTlb = false;
                if (self::isRedRow($sheet, $row, $colMapping)) {
                    $isTlb = true;
                }

                $patient = trim((string)$sheet->getCell([$colMapping['patient'], $row])->getValue());
                $doctor = trim((string)$sheet->getCell([$colMapping['doctor'], $row])->getValue());
                $tarifVal = $sheet->getCell([$colMapping['tarif'], $row])->getValue();
                $tindakan = $colMapping['tindakan'] !== -1 ? trim((string)$sheet->getCell([$colMapping['tindakan'], $row])->getValue()) : '';

                // Skip completely empty rows
                if ($patient === '' && $doctor === '' && $tarifVal === null) {
                    continue;
                }

                // Clean and format Tarif
                $tarif = 0.0;
                if ($tarifVal !== null) {
                    if ($tarifVal instanceof \PhpOffice\PhpSpreadsheet\Cell\Cell && $sheet->getCell([$colMapping['tarif'], $row])->isFormula()) {
                        $tarif = (float)$sheet->getCell([$colMapping['tarif'], $row])->getCalculatedValue();
                    } else {
                        $cleanedTarif = preg_replace('/[^\d\.\,\-]/', '', (string)$tarifVal);
                        if (strpos($cleanedTarif, ',') !== false && strpos($cleanedTarif, '.') !== false) {
                            $cleanedTarif = str_replace(',', '', $cleanedTarif);
                        } elseif (strpos($cleanedTarif, ',') !== false) {
                            $cleanedTarif = str_replace(',', '.', $cleanedTarif);
                        }
                        $tarif = (float)$cleanedTarif;
                    }
                }

                // Parse Radiologi value (optional column)
                $radiologi = 0.0;
                if ($colMapping['radiologi'] !== -1) {
                    $radVal = $sheet->getCell([$colMapping['radiologi'], $row])->getValue();
                    if ($radVal !== null && $radVal !== '') {
                        $cleanedRad = preg_replace('/[^\d\.\,\-]/', '', (string)$radVal);
                        if (strpos($cleanedRad, ',') !== false && strpos($cleanedRad, '.') !== false) {
                            $cleanedRad = str_replace(',', '', $cleanedRad);
                        } elseif (strpos($cleanedRad, ',') !== false) {
                            $cleanedRad = str_replace(',', '.', $cleanedRad);
                        }
                        $radiologi = max(0.0, (float)$cleanedRad);
                    }
                }

                // Parse Tanggal (optional column)
                $tanggal = '';
                if ($colMapping['tanggal'] !== -1) {
                    $cell = $sheet->getCell([$colMapping['tanggal'], $row]);
                    if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                        $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cell->getValue())->format('d-M-y'); // Format: 02-Jan-26
                    } else {
                        $rawTanggal = trim((string)$cell->getValue());
                        if ($rawTanggal !== '') {
                            // Coba parse format dd/mm/yyyy atau yyyy-mm-dd
                            $parsedDate = date_create_from_format('d/m/Y', $rawTanggal);
                            if (!$parsedDate) {
                                // Fallback ke strtotime bawaan PHP (bisa baca format lain seperti 2026-01-02)
                                $parsedDate = date_create($rawTanggal);
                            }
                            
                            if ($parsedDate !== false) {
                                $tanggal = $parsedDate->format('d-M-y');
                            } else {
                                $tanggal = $rawTanggal;
                            }
                        }
                    }
                }

                $rowData = [
                    'row'          => $row,
                    'patient_name' => $patient,
                    'doctor_name'  => $doctor,
                    'tarif'        => $tarif,
                    'radiologi'    => $radiologi,
                    'tindakan'     => $tindakan,
                    'tanggal'      => $tanggal,
                    'is_tlb'       => $isTlb,
                    'is_valid'     => true,
                    'errors'       => []
                ];

                // Validate row data
                if ($patient === '') {
                    $rowData['is_valid'] = false;
                    $rowData['errors'][] = 'Nama Pasien kosong';
                }
                if ($doctor === '') {
                    $rowData['is_valid'] = false;
                    $rowData['errors'][] = 'Nama DPJP kosong';
                }
                if ($tarif <= 0) {
                    $rowData['is_valid'] = false;
                    $rowData['errors'][] = 'Tarif kosong atau bernilai nol/negatif';
                }

                // Duplicate Detection (Include tanggal and tindakan so different dates/procedures are not flagged as duplicate)
                $hashKey = md5(strtoupper($patient) . '|' . strtoupper($doctor) . '|' . (string)$tarif . '|' . strtoupper((string)$tanggal) . '|' . strtoupper((string)$tindakan));
                if (isset($seenRows[$hashKey])) {
                    $seenRows[$hashKey][] = $row;
                    $duplicates[] = [
                        'row'          => $row,
                        'original_row' => $seenRows[$hashKey][0],
                        'patient_name' => $patient,
                        'doctor_name'  => $doctor,
                        'tarif'        => $tarif,
                        'tanggal'      => $tanggal,
                        'tindakan'     => $tindakan
                    ];
                } else {
                    $seenRows[$hashKey] = [$row];
                }

                $rows[] = $rowData;
            }

            return [
                'success'      => true,
                'total_rows'   => count($rows),
                'header_row'   => $headerRow,
                'headers'      => $headers,
                'rows'         => $rows,
                'duplicates'   => $duplicates,
                'col_mapping'  => $colMapping
            ];

        } catch (Exception $e) {
            Helper::logSystemError("Excel reading failed: " . $e->getMessage(), $e->getTraceAsString());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Check if a header matches any in the allowed patterns.
     */
    private static function matchesHeader(string $val, array $patterns): bool {
        $val = trim($val);
        foreach ($patterns as $pattern) {
            if ($val === $pattern || strpos($val, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if a row has a red background fill (indicating an ignored/cancelled row).
     */
    private static function isRedRow($sheet, int $row, array $colMapping): bool {
        foreach ($colMapping as $colIdx) {
            if ($colIdx === -1) continue;
            $fill = $sheet->getCell([$colIdx, $row])->getStyle()->getFill();
            if ($fill->getFillType() !== \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE) {
                $rgb = strtoupper($fill->getStartColor()->getRGB());
                if (self::isRedRgb($rgb)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Determine if an RGB hex color represents a red fill.
     */
    private static function isRedRgb(string $rgb): bool {
        if ($rgb === '' || $rgb === 'FFFFFF' || $rgb === '000000') {
            return false;
        }

        $knownReds = [
            'C00000', 'FF0000', '9C0006', 'FFC7CE', 'E6B8B8', 
            'FF5252', 'ED1C24', '880000', 'CC0000', '990000', 
            'FF4D4D', 'B22222', 'DC143C', 'CD5C5C', 'A71D2A'
        ];
        if (in_array($rgb, $knownReds, true)) {
            return true;
        }

        if (strlen($rgb) === 6) {
            $r = hexdec(substr($rgb, 0, 2));
            $g = hexdec(substr($rgb, 2, 2));
            $b = hexdec(substr($rgb, 4, 2));

            // Red component dominant over green and blue
            if ($r > 120 && $r > ($g * 1.3) && $r > ($b * 1.3)) {
                return true;
            }
        }

        return false;
    }
}
