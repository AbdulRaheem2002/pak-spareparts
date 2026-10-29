<?php
/**
 * PakAutoParts - Lightweight Base Active Record / Model
 * Clean PDO query builder with auto-pagination and SQL injection prevention.
 */

declare(strict_types=1);

namespace App\Core;

abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    public static function find(int $id): ?array
    {
        $table = static::$table;
        $pk = static::$primaryKey;
        return Database::selectOne("SELECT * FROM `{$table}` WHERE `{$pk}` = :id LIMIT 1", ['id' => $id]);
    }

    public static function findBy(string $column, mixed $value): ?array
    {
        $table = static::$table;
        // Escape column name safely
        $cleanCol = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
        return Database::selectOne("SELECT * FROM `{$table}` WHERE `{$cleanCol}` = :val LIMIT 1", ['val' => $value]);
    }

    public static function all(string $orderBy = 'id DESC', ?int $limit = null): array
    {
        $table = static::$table;
        $sql = "SELECT * FROM `{$table}` ORDER BY {$orderBy}";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }
        return Database::select($sql);
    }

    public static function where(string $where, array $params = [], string $orderBy = 'id DESC', ?int $limit = null): array
    {
        $table = static::$table;
        $sql = "SELECT * FROM `{$table}` WHERE {$where} ORDER BY {$orderBy}";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }
        return Database::select($sql, $params);
    }

    public static function whereOne(string $where, array $params = []): ?array
    {
        $table = static::$table;
        $sql = "SELECT * FROM `{$table}` WHERE {$where} LIMIT 1";
        return Database::selectOne($sql, $params);
    }

    public static function count(string $where = '1=1', array $params = []): int
    {
        $table = static::$table;
        $row = Database::selectOne("SELECT COUNT(*) as total FROM `{$table}` WHERE {$where}", $params);
        return (int)($row['total'] ?? 0);
    }

    public static function create(array $data): int
    {
        $table = static::$table;
        return Database::insert($table, $data);
    }

    public static function updateById(int $id, array $data): int
    {
        $table = static::$table;
        $pk = static::$primaryKey;
        return Database::update($table, $data, "`{$pk}` = :pk_id", ['pk_id' => $id]);
    }

    public static function deleteById(int $id): int
    {
        $table = static::$table;
        $pk = static::$primaryKey;
        return Database::delete($table, "`{$pk}` = :pk_id", ['pk_id' => $id]);
    }

    public static function paginate(
        int $page = 1,
        int $perPage = 24,
        string $where = '1=1',
        array $params = [],
        string $orderBy = 'id DESC',
        string $select = '*'
    ): array {
        $table = static::$table;
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));

        $total = self::count($where, $params);
        $totalPages = (int)ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT {$select} FROM `{$table}` WHERE {$where} ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}";
        $items = Database::select($sql, $params);

        return [
            'data'         => $items,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => $totalPages,
            'from'         => $total > 0 ? $offset + 1 : 0,
            'to'           => min($offset + $perPage, $total),
            'has_more'     => $page < $totalPages,
        ];
    }
}

