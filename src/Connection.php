<?php

namespace Hexlet\Code;

use PDO;
use PDOException;
use Exception;

final class Connection
{
    private static ?self $connection = null;

    public function connect(): PDO
    {
        $params = $this->getDatabaseParams();

        $dsn = sprintf(
            "pgsql:host=%s;port=%d;dbname=%s",
            $params['host'],
            $params['port'],
            $params['database']
        );

        $pdo = new PDO($dsn, $params['user'], $params['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    private function getDatabaseParams(): array
    {
        $databaseUrl = getenv('DATABASE_URL');

        if ($databaseUrl) {
            return $this->parseDatabaseUrl($databaseUrl);
        }

        throw new Exception("Error reading database configuration");
    }

    private function parseDatabaseUrl(string $databaseUrl): array
    {
        $url = parse_url($databaseUrl);

        return [
            'host' => $url['host'] ?? 'localhost',
            'port' => $url['port'] ?? 5432,
            'database' => isset($url['path']) ? ltrim($url['path'], '/') : '',
            'user' => $url['user'] ?? '',
            'password' => $url['pass'] ?? ''
        ];
    }

    public static function get(): self
    {
        if (self::$connection === null) {
            self::$connection = new self();
        }

        return self::$connection;
    }
}
