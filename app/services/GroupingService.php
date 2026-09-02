<?php
declare(strict_types=1);

class GroupingService {
    private array $doctorMapping  = [];  // normalizedName => mappingData
    private array $doctorTokens   = [];  // normalizedName => tokenArray (pre-computed at load)
    private array $fuzzyMatchCache = [];
    private array $normalizeCache  = [];

    public function __construct() {
        $this->loadDoctorMapping();
    }

    /**
     * Load doctors and aliases from DB to build normalization lookup map.
     */
    private function loadDoctorMapping(): void {
        try {
            $db = Database::getConnection();

            // 1. Get official doctor names mapped to department
            $stmt = $db->query("SELECT p.id AS doctor_id, p.doctor_name, d.id AS department_id, d.department_name
                                FROM dpjp p
                                JOIN departments d ON p.department_id = d.id
                                WHERE d.status = 'active'");

            while ($row = $stmt->fetch()) {
                $officialName       = $row['doctor_name'];
                $normalizedOfficial = Doctor::normalizeName($officialName);

                $mappingData = [
                    'doctor_id'       => (int)$row['doctor_id'],
                    'official_name'   => $officialName,
                    'department_id'   => (int)$row['department_id'],
                    'department_name' => $row['department_name']
                ];

                $this->doctorMapping[$normalizedOfficial] = $mappingData;
                $this->doctorTokens[$normalizedOfficial]  = Doctor::nameTokens($normalizedOfficial);
            }

            // 2. Load aliases
            $stmtAliases = $db->query("SELECT a.alias_name, p.id AS doctor_id, p.doctor_name, d.id AS department_id, d.department_name
                                       FROM dpjp_aliases a
                                       JOIN dpjp p ON a.dpjp_id = p.id
                                       JOIN departments d ON p.department_id = d.id
                                       WHERE d.status = 'active'");

            while ($row = $stmtAliases->fetch()) {
                $normalizedAlias = strtoupper(trim($row['alias_name']));

                if (!isset($this->doctorMapping[$normalizedAlias])) {
                    $this->doctorMapping[$normalizedAlias] = [
                        'doctor_id'       => (int)$row['doctor_id'],
                        'official_name'   => $row['doctor_name'],
                        'department_id'   => (int)$row['department_id'],
                        'department_name' => $row['department_name']
                    ];
                    $this->doctorTokens[$normalizedAlias] = Doctor::nameTokens($normalizedAlias);
                }
            }
        } catch (Exception $e) {
            Helper::logSystemError("Load doctor mapping error: " . $e->getMessage(), $e->getTraceAsString());
        }
    }

    /**
     * Find best-matching doctor for a given raw name.
     * Step 1: Exact normalized lookup  — O(1)
     * Step 2: Cache hit               — O(1)
     * Step 3: Substring containment   — O(n) string ops
     * Step 4: Word-token Jaccard      — O(doctors × tokens), very fast
     */
    private function resolveDoctor(string $rawName): ?array {
        // Normalize with local cache to avoid repeated regex
        if (!isset($this->normalizeCache[$rawName])) {
            $this->normalizeCache[$rawName] = Doctor::normalizeName($rawName);
        }
        $normalizedName = $this->normalizeCache[$rawName];

        // Step 1: Exact match
        if (isset($this->doctorMapping[$normalizedName])) {
            return $this->doctorMapping[$normalizedName];
        }

        // Step 2: Previously cached fuzzy result
        if (array_key_exists($normalizedName, $this->fuzzyMatchCache)) {
            return $this->fuzzyMatchCache[$normalizedName];
        }

        // Step 3 + 4: Compute then cache
        $inputTokens = Doctor::nameTokens($normalizedName);
        $bestScore   = 0.0;
        $bestMatch   = null;
        $bestFuzzy   = 0.0;
        $bestFuzzyMatch = null;

        foreach ($this->doctorMapping as $normOfficial => $map) {
            // Step 3: Substring containment (O(n) string scan)
            $lenOff = strlen($normOfficial);
            $lenIn  = strlen($normalizedName);
            if ($lenOff > 4 && $lenIn > 4) {
                if (strpos($normalizedName, $normOfficial) !== false ||
                    strpos($normOfficial, $normalizedName) !== false) {
                    $this->fuzzyMatchCache[$normalizedName] = $map;
                    return $map;
                }
            }

            // Step 4a: Word-token Jaccard (no char-by-char, very fast)
            $officialTokens = $this->doctorTokens[$normOfficial] ?? Doctor::nameTokens($normOfficial);
            $score = Doctor::tokenJaccard($inputTokens, $officialTokens);
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $map;
            }

            // Step 4b: Track best levenshtein score in parallel (cheap for short tokens)
            $fuzzyScore = Doctor::tokenFuzzyScore($inputTokens, $officialTokens);
            if ($fuzzyScore > $bestFuzzy) {
                $bestFuzzy      = $fuzzyScore;
                $bestFuzzyMatch = [$map, $officialTokens];
            }
        }

        // Accept Jaccard match at >= 0.5
        if ($bestScore >= 0.5) {
            $this->fuzzyMatchCache[$normalizedName] = $bestMatch;
            return $bestMatch;
        }

        // Fallback: token-level levenshtein for typo names (e.g. PRHASANTI vs PRAHASANTI)
        // Only accept if >= 0.72 average character similarity per token
        if ($bestFuzzy >= 0.72 && $bestFuzzyMatch !== null) {
            $result = $bestFuzzyMatch[0];
            $this->fuzzyMatchCache[$normalizedName] = $result;
            return $result;
        }

        $this->fuzzyMatchCache[$normalizedName] = null;
        return null;
    }

    /**
     * Group parsed rows by department and calculate Jaspel.
     */
    public function group(array $rows, CalculationService $calcService): array {
        $groupedData     = [];
        $errors          = [];
        $unmappedDoctors = [];
        $totalJaspel     = 0.0;
        $successCount    = 0;

        // Accumulator for RKG (Radiologi Kedokteran Gigi)
        $rkgTotalTarif  = 0.0;
        $rkgTotalJaspel = 0.0;

        $rkgDeptName = defined('RKG_DEPT_NAME') ? RKG_DEPT_NAME : 'Radiologi Kedokteran Gigi';
        $rkgLabel    = defined('RKG_DPJP_LABEL') ? RKG_DPJP_LABEL : 'DPJP RKG';

        foreach ($rows as $rowData) {
            $rowNum = $rowData['row'];

            // Row-level validation errors (empty patient, empty tarif, etc.)
            if (!$rowData['is_valid']) {
                $errors[] = [
                    'row'          => $rowNum,
                    'patient_name' => $rowData['patient_name'],
                    'doctor_name'  => $rowData['doctor_name'],
                    'tarif'        => $rowData['tarif'],
                    'tindakan'     => $rowData['tindakan'] ?? '',
                    'tanggal'      => $rowData['tanggal'] ?? '',
                    'message'      => implode(', ', $rowData['errors'])
                ];
                continue;
            }

            $rawDoctorName = $rowData['doctor_name'];
            $matchedData   = $this->resolveDoctor($rawDoctorName);

            if ($matchedData !== null) {
                $deptName     = $matchedData['department_name'];
                $officialName = $matchedData['official_name'];

                $radiologi = (float)($rowData['radiologi'] ?? 0.0);
                $totalTarif = (float)$rowData['tarif'];

                if ($radiologi > 0.0) {
                    // Tarif for doctor's dept = TOTAL_TARIF − RADIOLOGI
                    $netTarif = $totalTarif - $radiologi;
                    $jaspel   = $calcService->calculate($netTarif);

                    $transaction = [
                        'row_number'   => $rowNum,
                        'patient_name' => $rowData['patient_name'],
                        'doctor_id'    => $matchedData['doctor_id'],
                        'doctor_name'  => $officialName,
                        'tarif'        => $netTarif,
                        'jaspel'       => $jaspel,
                        'tindakan'     => $rowData['tindakan'] ?? '',
                        'tanggal'      => $rowData['tanggal'] ?? ''
                    ];

                    if (!isset($groupedData[$deptName])) {
                        $groupedData[$deptName] = [];
                    }
                    $groupedData[$deptName][] = $transaction;
                    $totalJaspel += $jaspel;

                    // Add individual RADIOLOGI transaction into RKG dept
                    $rkgJaspel = $calcService->calculateRkg($radiologi);
                    $rkgTotalJaspel += $rkgJaspel;
                    $totalJaspel    += $rkgJaspel;

                    $rkgTransaction = [
                        'row_number'   => $rowNum,
                        'patient_name' => $rowData['patient_name'],
                        'doctor_id'    => 0,
                        'doctor_name'  => $rkgLabel,
                        'tarif'        => $radiologi,
                        'jaspel'       => $rkgJaspel,
                        'tindakan'     => $rowData['tindakan'] ?? '',
                        'tanggal'      => $rowData['tanggal'] ?? ''
                    ];

                    if (!isset($groupedData[$rkgDeptName])) {
                        $groupedData[$rkgDeptName] = [];
                    }
                    $groupedData[$rkgDeptName][] = $rkgTransaction;

                } else {
                    // No radiologi: process normally
                    $jaspel = $calcService->calculate($totalTarif);
                    $totalJaspel += $jaspel;

                    $transaction = [
                        'row_number'   => $rowNum,
                        'patient_name' => $rowData['patient_name'],
                        'doctor_id'    => $matchedData['doctor_id'],
                        'doctor_name'  => $officialName,
                        'tarif'        => $totalTarif,
                        'jaspel'       => $jaspel,
                        'tindakan'     => $rowData['tindakan'] ?? '',
                        'tanggal'      => $rowData['tanggal'] ?? ''
                    ];

                    if (!isset($groupedData[$deptName])) {
                        $groupedData[$deptName] = [];
                    }
                    $groupedData[$deptName][] = $transaction;
                }

                $successCount++;
            } else {
                $errors[] = [
                    'row'          => $rowNum,
                    'patient_name' => $rowData['patient_name'],
                    'doctor_name'  => $rawDoctorName,
                    'tarif'        => $rowData['tarif'],
                    'tindakan'     => $rowData['tindakan'] ?? '',
                    'tanggal'      => $rowData['tanggal'] ?? '',
                    'message'      => "DPJP '{$rawDoctorName}' tidak ditemukan di database Master Data"
                ];

                if (!in_array($rawDoctorName, $unmappedDoctors)) {
                    $unmappedDoctors[] = $rawDoctorName;
                }
            }
        }

        return [
            'grouped'          => $groupedData,
            'errors'           => $errors,
            'unmapped_doctors' => $unmappedDoctors,
            'total_jaspel'     => $totalJaspel,
            'success_count'    => $successCount,
            'failed_count'     => count($errors)
        ];
    }
}



