<?php
declare(strict_types=1);

class Doctor {
    /**
     * Get all doctors with department details.
     */
    public static function getAll(string $search = '', int $departmentId = 0): array {
        try {
            $db = Database::getConnection();
            $query = "SELECT p.*, d.department_name, d.status AS department_status
                      FROM dpjp p 
                      JOIN departments d ON p.department_id = d.id";
            
            $where = [];
            $params = [];

            if ($search !== '') {
                $where[] = "(p.doctor_name LIKE ? OR EXISTS (
                    SELECT 1 FROM dpjp_aliases a WHERE a.dpjp_id = p.id AND a.alias_name LIKE ?
                ))";
                $params[] = '%' . $search . '%';
                $params[] = '%' . $search . '%';
            }

            if ($departmentId > 0) {
                $where[] = "p.department_id = ?";
                $params[] = $departmentId;
            }

            if (!empty($where)) {
                $query .= " WHERE " . implode(" AND ", $where);
            }

            $query .= " ORDER BY p.doctor_name ASC";
            
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            Helper::logSystemError("Get all doctors error: " . $e->getMessage(), $e->getTraceAsString());
            return [];
        }
    }

    public static function getById(int $id): ?array {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT p.*, d.department_name FROM dpjp p JOIN departments d ON p.department_id = d.id WHERE p.id = ? LIMIT 1");
            $stmt->execute([$id]);
            $data = $stmt->fetch();
            return $data ?: null;
        } catch (Exception $e) {
            Helper::logSystemError("Get doctor by ID error: " . $e->getMessage(), $e->getTraceAsString());
            return null;
        }
    }

    public static function create(string $name, int $departmentId): bool {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO dpjp (doctor_name, department_id) VALUES (?, ?)");
            $success = $stmt->execute([$name, $departmentId]);
            if ($success) {
                $doctorId = (int)$db->lastInsertId();
                // Auto create normalized alias
                self::addAlias($doctorId, self::normalizeName($name));
            }
            return $success;
        } catch (Exception $e) {
            Helper::logSystemError("Create doctor error: " . $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    public static function update(int $id, string $name, int $departmentId): bool {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("UPDATE dpjp SET doctor_name = ?, department_id = ? WHERE id = ?");
            $success = $stmt->execute([$name, $departmentId, $id]);
            if ($success) {
                // Check if normalized alias exists, if not create it
                $normalized = self::normalizeName($name);
                $stmtAlias = $db->prepare("SELECT COUNT(*) FROM dpjp_aliases WHERE dpjp_id = ? AND alias_name = ?");
                $stmtAlias->execute([$id, $normalized]);
                if ((int)$stmtAlias->fetchColumn() === 0) {
                    self::addAlias($id, $normalized);
                }
            }
            return $success;
        } catch (Exception $e) {
            Helper::logSystemError("Update doctor error: " . $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    public static function delete(int $id): bool {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM dpjp WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            Helper::logSystemError("Delete doctor error: " . $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    // --- Aliases management ---

    public static function getAliases(int $doctorId): array {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM dpjp_aliases WHERE dpjp_id = ? ORDER BY alias_name ASC");
            $stmt->execute([$doctorId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            Helper::logSystemError("Get aliases error: " . $e->getMessage(), $e->getTraceAsString());
            return [];
        }
    }

    public static function addAlias(int $doctorId, string $aliasName): bool {
        try {
            $db = Database::getConnection();
            $aliasName = strtoupper(trim($aliasName));
            if ($aliasName === '') return false;

            // Check duplicate alias
            $stmtCheck = $db->prepare("SELECT COUNT(*) FROM dpjp_aliases WHERE alias_name = ?");
            $stmtCheck->execute([$aliasName]);
            if ((int)$stmtCheck->fetchColumn() > 0) {
                return false;
            }

            $stmt = $db->prepare("INSERT INTO dpjp_aliases (dpjp_id, alias_name) VALUES (?, ?)");
            return $stmt->execute([$doctorId, $aliasName]);
        } catch (Exception $e) {
            Helper::logSystemError("Add alias error: " . $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    public static function deleteAlias(int $aliasId): bool {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM dpjp_aliases WHERE id = ?");
            return $stmt->execute([$aliasId]);
        } catch (Exception $e) {
            Helper::logSystemError("Delete alias error: " . $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    public static function addAliasByDoctorName(string $doctorName, string $aliasName): bool {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT id FROM dpjp WHERE doctor_name = ? LIMIT 1");
            $stmt->execute([$doctorName]);
            $doctorId = $stmt->fetchColumn();
            if ($doctorId) {
                return self::addAlias((int)$doctorId, $aliasName);
            }
            return false;
        } catch (Exception $e) {
            Helper::logSystemError("Add alias by name error: " . $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Strip all medical credentials and punctuation, returning only CORE NAME TOKENS in uppercase.
     * E.g. "Prof. Dr. David B. Kamadjaja, drg., MDS., Sp.BM(K)" → "DAVID B KAMADJAJA"
     */
    public static function normalizeName(string $name): string {
        $name = strtolower($name);

        // Remove text inside parentheses (specialization markers like (K), (M))
        $name = preg_replace('/\(.*?\)/', ' ', $name);

        // Strip text after comma (degree suffixes like ", drg., MDS., Sp.BM")
        if (strpos($name, ',') !== false) {
            $name = explode(',', $name)[0];
        }

        // Remove well-known medical title/credential words (word boundary match)
        $name = preg_replace('/\b(?:prof|dr|drg|sp[a-z]*|m\.?kes|m\.?ds|m\.?sc|mars|ph\.?d|fics|su|subsp|subs|ortognat|perio|k|g)\b/', ' ', $name);

        // Replace all punctuation and dots with spaces
        $name = preg_replace('/[^\w\s]/u', ' ', $name);

        // Remove single-letter tokens (initials like "D.", "B.") that are left after stripping
        $name = preg_replace('/\b[a-z]\b/', ' ', $name);

        // Normalize whitespace
        $name = preg_replace('/\s+/', ' ', $name);

        return strtoupper(trim($name));
    }

    /**
     * Split a normalized name into a set of unique tokens for word-level matching.
     */
    public static function nameTokens(string $normalizedName): array {
        $tokens = explode(' ', $normalizedName);
        // Filter short tokens (initials, stray letters)
        return array_values(array_unique(array_filter($tokens, fn($t) => strlen($t) >= 3)));
    }

    /**
     * Jaccard similarity between two token sets (0.0 – 1.0).
     */
    public static function tokenJaccard(array $tokensA, array $tokensB): float {
        if (empty($tokensA) || empty($tokensB)) return 0.0;
        $intersection = array_intersect($tokensA, $tokensB);
        $union = array_unique(array_merge($tokensA, $tokensB));
        return count($intersection) / count($union);
    }

    /**
     * Fuzzy token similarity: best-match each token in A against all tokens in B
     * using levenshtein distance. Returns average best-match ratio (0.0-1.0).
     * Only called as a last resort for short-name typo cases.
     */
    public static function tokenFuzzyScore(array $tokensA, array $tokensB): float {
        if (empty($tokensA) || empty($tokensB)) return 0.0;
        $totalScore = 0.0;
        foreach ($tokensA as $tA) {
            $lenA     = strlen($tA);
            $bestRatio = 0.0;
            foreach ($tokensB as $tB) {
                $maxLen = max($lenA, strlen($tB));
                if ($maxLen === 0) continue;
                $dist  = levenshtein($tA, $tB);
                $ratio = 1.0 - ($dist / $maxLen);
                if ($ratio > $bestRatio) $bestRatio = $ratio;
            }
            $totalScore += $bestRatio;
        }
        return $totalScore / count($tokensA);
    }
}
