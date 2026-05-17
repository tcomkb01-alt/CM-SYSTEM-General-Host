<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    protected string $table = 'users';

    public function findByUsername(string $username): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
        return $this->db->queryOne($sql, [':username' => $username]);
    }

    public function updateLoginLog(int $userId): bool
    {
        $sql = "UPDATE {$this->table} SET last_login_at = NOW() WHERE id = :id";
        return $this->db->execute($sql, [':id' => $userId]);
    }

    public function incrementFailedAttempts(string $username): void
    {
        $sql = "UPDATE {$this->table} SET failed_attempts = failed_attempts + 1 WHERE username = :username";
        $this->db->execute($sql, [':username' => $username]);
    }

    public function resetFailedAttempts(int $userId): void
    {
        $sql = "UPDATE {$this->table} SET failed_attempts = 0 WHERE id = :id";
        $this->db->execute($sql, [':id' => $userId]);
    }
}
