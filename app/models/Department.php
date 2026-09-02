<?php
declare(strict_types=1);

class Department {
    /**
     * Get all departments with their doctor counts.
     */
    public static function getAll(string $search = ''): array {
        try {
            $db = Database::getConnection();
            $query = "SELECT d.*, COUNT(p.id) AS doctor_count 
                      FROM departments d 
                      LEFT JOIN dpjp p ON d.id = p.department_id";
            
            $params = [];
            if ($search !== '') {
                $query .= " WHERE d.department_name LIKE ?";
                $params[] = '%' . $search . '%';
            }
            
            $query .= " GROUP BY d.id ORDER BY d.department_name ASC";
            
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            Helper::logSystemError("Get all departments error: " . $e->getMessage(), $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Get active departments list (name to id mapping).
     */
    public static function getActiveList(): array {
        try {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT id, department_name FROM departments WHERE status = 'active' ORDER BY department_name ASC");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            Helper::logSystemError("Get active departments error: " . $e->getMessage(), $e->getTraceAsString());
            return [];
        }
    }

    public static function getById(int $id): ?array {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM departments WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $data = $stmt->fetch();
            return $data ?: null;
        } catch (Exception $e) {
            Helper::logSystemError("Get department by ID error: " . $e->getMessage(), $e->getTraceAsString());
            return null;
        }
    }

    public static function create(string $name, string $status = 'active'): bool {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO departments (department_name, status) VALUES (?, ?)");
            return $stmt->execute([$name, $status]);
        } catch (Exception $e) {
            Helper::logSystemError("Create department error: " . $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    public static function update(int $id, string $name, string $status): bool {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("UPDATE departments SET department_name = ?, status = ? WHERE id = ?");
            return $stmt->execute([$name, $status, $id]);
        } catch (Exception $e) {
            Helper::logSystemError("Update department error: " . $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Delete department, checking if any doctors are currently assigned.
     */
    public static function delete(int $id): array {
        try {
            $db = Database::getConnection();
            
            // Check doctor counts
            $stmt = $db->prepare("SELECT COUNT(*) FROM dpjp WHERE department_id = ?");
            $stmt->execute([$id]);
            $count = (int)$stmt->fetchColumn();

            if ($count > 0) {
                return [
                    'success' => false,
                    'message' => "Departemen masih digunakan oleh {$count} DPJP. Silakan pindahkan atau hapus DPJP terlebih dahulu."
                ];
            }

            $stmt = $db->prepare("DELETE FROM departments WHERE id = ?");
            $success = $stmt->execute([$id]);
            return [
                'success' => $success,
                'message' => $success ? "Departemen berhasil dihapus." : "Gagal menghapus departemen."
            ];
        } catch (Exception $e) {
            Helper::logSystemError("Delete department error: " . $e->getMessage(), $e->getTraceAsString());
            return [
                'success' => false,
                'message' => "Error sistem saat menghapus: " . $e->getMessage()
            ];
        }
    }
}
