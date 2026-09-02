<?php
declare(strict_types=1);

class User {
    public int $id;
    public string $username;
    public string $role;
    public string $created_at;

    public static function authenticate(string $username, string $password): ?self {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $userData = $stmt->fetch();

            if ($userData && password_verify($password, $userData['password'])) {
                $user = new self();
                $user->id = (int)$userData['id'];
                $user->username = $userData['username'];
                $user->role = $userData['role'];
                $user->created_at = $userData['created_at'];
                return $user;
            }
        } catch (Exception $e) {
            Helper::logSystemError("Authentication failure: " . $e->getMessage(), $e->getTraceAsString());
        }
        return null;
    }

    public static function getById(int $id): ?self {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $userData = $stmt->fetch();

            if ($userData) {
                $user = new self();
                $user->id = (int)$userData['id'];
                $user->username = $userData['username'];
                $user->role = $userData['role'];
                $user->created_at = $userData['created_at'];
                return $user;
            }
        } catch (Exception $e) {
            Helper::logSystemError("Get user by ID failure: " . $e->getMessage(), $e->getTraceAsString());
        }
        return null;
    }
}
