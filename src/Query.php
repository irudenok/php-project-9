<?php

namespace Hexlet\Code;

use PDO;

class Query
{
    public function __construct(
        private PDO $pdo,
        private string $table
    // phpcs:ignore error
    ) {
    }

    public function insertValues(string $name, string $created_at): int
    {
        $sql = "INSERT INTO {$this->table}(name, created_at) VALUES(:name, :created_at)";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':created_at', $created_at);
        $stmt->execute();

        return $this->pdo->lastInsertId();
    }

    public function insertValuesChecks(array $check): int
    {
        $sql = "INSERT INTO {$this->table}(url_id, status_code, h1, title, description, created_at)
                VALUES(:url_id, :status_code, :h1, :title, :description, :created_at)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':url_id', $check['url_id']);
        $stmt->bindValue(':status_code', $check['status_code'] ?? null);
        $stmt->bindValue(':h1', $check['h1'] ?? null);
        $stmt->bindValue(':title', $check['title'] ?? null);
        $stmt->bindValue(':description', $check['description'] ?? null);
        $stmt->bindValue(':created_at', $check['date']);
        $stmt->execute();

        return $this->pdo->lastInsertId();
    }
}
