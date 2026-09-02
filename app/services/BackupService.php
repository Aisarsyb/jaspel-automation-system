<?php
declare(strict_types=1);

class BackupService {
    /**
     * Export the database schema and data as a SQL dump string.
     */
    public static function exportSql(): string {
        try {
            $db = Database::getConnection();
            $sql = "-- SQL Dump for Jaspel Automation System (JAS)\n";
            $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            // Get all tables
            $tables = [];
            $result = $db->query("SHOW TABLES");
            while ($row = $result->fetch(PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }

            foreach ($tables as $table) {
                // Drop Table statement
                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";

                // Show Create Table statement
                $stmt = $db->query("SHOW CREATE TABLE `{$table}`");
                $createTableRow = $stmt->fetch();
                $sql .= $createTableRow['Create Table'] . ";\n\n";

                // Fetch table data
                $stmtData = $db->query("SELECT * FROM `{$table}`");
                $rows = $stmtData->fetchAll(PDO::FETCH_ASSOC);

                if (count($rows) > 0) {
                    $sql .= "INSERT INTO `{$table}` (";
                    $columns = array_keys($rows[0]);
                    $escapedCols = array_map(fn($col) => "`{$col}`", $columns);
                    $sql .= implode(", ", $escapedCols) . ") VALUES\n";

                    $valueLines = [];
                    foreach ($rows as $row) {
                        $values = [];
                        foreach ($columns as $col) {
                            $val = $row[$col];
                            if ($val === null) {
                                $values[] = "NULL";
                            } else {
                                $escapedVal = addslashes((string)$val);
                                $values[] = "'{$escapedVal}'";
                            }
                        }
                        $valueLines[] = "(" . implode(", ", $values) . ")";
                    }
                    $sql .= implode(",\n", $valueLines) . ";\n\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
            return $sql;

        } catch (Exception $e) {
            Helper::logSystemError("Database backup export failure: " . $e->getMessage(), $e->getTraceAsString());
            throw new RuntimeException("Gagal mencadangkan database: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Restore the database using a SQL dump string.
     */
    public static function restoreSql(string $sqlContent): bool {
        try {
            $db = Database::getConnection();
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Disable foreign keys temporarily
            $db->exec("SET FOREIGN_KEY_CHECKS=0;");

            // Basic parser: split by semicolon + newline or newline + semicolon
            // To prevent splitting values containing semicolons, we can do a line-by-line check
            $queries = [];
            $currentQuery = '';
            $lines = explode("\n", $sqlContent);

            foreach ($lines as $line) {
                // Skip comments and empty lines
                $trimmed = trim($line);
                if ($trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '#')) {
                    continue;
                }

                $currentQuery .= $line . "\n";

                // If line ends with a semicolon, it's the end of a query
                if (str_ends_with($trimmed, ';')) {
                    $queries[] = $currentQuery;
                    $currentQuery = '';
                }
            }

            // In case the last query doesn't end with semicolon but has content
            if (trim($currentQuery) !== '') {
                $queries[] = $currentQuery;
            }

            // Execute all queries
            foreach ($queries as $query) {
                $trimmedQuery = trim($query);
                if ($trimmedQuery !== '') {
                    $db->exec($trimmedQuery);
                }
            }

            $db->exec("SET FOREIGN_KEY_CHECKS=1;");
            return true;

        } catch (Exception $e) {
            Helper::logSystemError("Database restore failure: " . $e->getMessage(), $e->getTraceAsString());
            try {
                // Ensure foreign keys are re-enabled even after failure
                $db = Database::getConnection();
                $db->exec("SET FOREIGN_KEY_CHECKS=1;");
            } catch (Exception $e2) {
                // ignore
            }
            return false;
        }
    }
}
