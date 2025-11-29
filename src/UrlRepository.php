<?php

namespace Hexlet\Code;

use PDO;

class UrlRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function save(string $name, string $createdAt): int
    {
        $sql = "INSERT INTO urls (name, created_at) VALUES (:name, :created_at)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'created_at' => $createdAt
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT id, name, created_at FROM urls WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByName(string $name): ?array
    {
        $sql = "SELECT id, name, created_at FROM urls WHERE name = :name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['name' => $name]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findAll(): array
    {
        $sql = "SELECT id, name, created_at FROM urls ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
