<?php

namespace Hexlet\Code;

use PDO;

class UrlCheckRepository
{
    public function __construct(
        private PDO $pdo
    // phpcs:ignore error
    ) {
    }

    public function save(array $check): int
    {
        $sql = "INSERT INTO url_checks (url_id, status_code, h1, title, description, created_at)
                VALUES (:url_id, :status_code, :h1, :title, :description, :created_at)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':url_id', $check['url_id']);
        $stmt->bindValue(':status_code', $check['status_code'] ?? null);
        $stmt->bindValue(':h1', $check['h1'] ?? null);
        $stmt->bindValue(':title', $check['title'] ?? null);
        $stmt->bindValue(':description', $check['description'] ?? null);
        $stmt->bindValue(':created_at', $check['date']);
        $stmt->execute();

        return (int) $this->pdo->lastInsertId();
    }

    public function findLatestChecks(): array
    {
        $sql = "SELECT DISTINCT ON (url_id) url_id, created_at, status_code
                FROM url_checks
                ORDER BY url_id, created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUrlId(int $urlId): array
    {
        $sql = "SELECT * FROM url_checks WHERE url_id = :url_id ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':url_id', $urlId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
