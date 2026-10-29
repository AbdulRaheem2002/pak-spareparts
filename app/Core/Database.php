<?php
/**
 * PakAutoParts - Database Connection & PDO Helper
 * Strict prepared statements only, transaction support with row locking.
 */

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $config = Config::get('db');

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        try {
            self::$instance = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );
            return self::$instance;
        } catch (PDOException $e) {
            $logDir = Config::get('paths.logs');
            if (is_dir($logDir)) {
                file_put_contents(
                    $logDir . '/db_errors.log',
                    sprintf("[%s] DB Connection Error: %s\n", date('Y-m-d H:i:s'), $e->getMessage()),
                    FILE_APPEND | LOCK_EX
                );
            }

            if (Config::get('app.debug')) {
                throw new \RuntimeException("Database Connection Failed: " . $e->getMessage(), (int)$e->getCode(), $e);
            }

            die("A temporary database error occurred. Please try again shortly or contact support.");
        }
    }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $db = self::connect();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function select(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function selectOne(string $sql, array $params = []): ?array
    {
        $result = self::query($sql, $params)->fetch();
        return $result ?: null;
    }

    public static function insert(string $table, array $data): int
    {
        $db = self::connect();
        $columns = implode('`, `', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO `{$table}` (`{$columns}`) VALUES ({$placeholders})";
        self::query($sql, $data);

        return (int)$db->lastInsertId();
    }

    public static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $setClauses = [];
        $params = [];

        foreach ($data as $col => $val) {
            $paramKey = 'set_' . $col;
            $setClauses[] = "`{$col}` = :{$paramKey}";
            $params[$paramKey] = $val;
        }

        $setSql = implode(', ', $setClauses);
        $sql = "UPDATE `{$table}` SET {$setSql} WHERE {$where}";

        $mergedParams = array_merge($params, $whereParams);
        $stmt = self::query($sql, $mergedParams);

        return $stmt->rowCount();
    }

    public static function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM `{$table}` WHERE {$where}";
        return self::query($sql, $params)->rowCount();
    }

    public static function beginTransaction(): bool
    {
        return self::connect()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::connect()->commit();
    }

    public static function rollBack(): bool
    {
        if (self::connect()->inTransaction()) {
            return self::connect()->rollBack();
        }
        return false;
    }

    public static function inTransaction(): bool
    {
        return self::connect()->inTransaction();
    }

    public static function lastInsertId(): int
    {
        return (int)self::connect()->lastInsertId();
    }
}

