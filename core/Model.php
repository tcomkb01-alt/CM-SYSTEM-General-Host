<?php

namespace Core;

abstract class Model
{
    protected Database $db;
    protected string $table;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function all(): array
    {
        return $this->db->query("SELECT * FROM {$this->table}");
    }

    public function where(string $column, mixed $value): array
    {
        return $this->db->query("SELECT * FROM {$this->table} WHERE {$column} = ?", [$value]);
    }

    public function find(int $id): ?array
    {
        return $this->db->queryOne("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function create(array $data): string
    {
        $keys = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ({$keys}) VALUES ({$placeholders})";
        $this->db->execute($sql, array_values($data));
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = implode('=?,', array_keys($data)) . '=?';
        $sql = "UPDATE {$this->table} SET {$fields} WHERE id = ?";
        $params = array_values($data);
        $params[] = $id;
        return $this->db->execute($sql, $params);
    }

    public function delete(int $id): bool
    {
        return $this->db->execute("DELETE FROM {$this->table} WHERE id = ?", [$id]);
    }
}
