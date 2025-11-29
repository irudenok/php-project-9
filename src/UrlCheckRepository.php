<?php

namespace Hexlet\Code;

use PDO;

class UrlCheckRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function save(array $check): int
    {
        $sql = "INSERT INTO url_checks (url_id, status_code, h1, title, description, created_at)
                VALUES (:url_id, :status_code, :h1, :title, :description, :created_at)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'url_id' => $check['url_id'],
            'status_code' => $check['status_code'] ?? null,
            'h1' => $check['h1'] ?? null,
            'title' => $check['title'] ?? null,
            'description' => $check['description'] ?? null,
            'created_at' => $check['date']
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findLatestChecks(): array
    {
        $sql = "SELECT url_id, created_at, status_code 
                FROM url_checks 
                ORDER BY url_id, created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUrlId(int $urlId): array
    {
        $sql = "SELECT id, url_id, status_code, h1, title, description, created_at 
                FROM url_checks 
                WHERE url_id = :url_id 
                ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['url_id' => $urlId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
