<?php
/**
 * PakAutoParts - Vehicle Model
 * Fits the Pakistani automobile market: hierarchical Make -> Model -> Year -> Engine lookups.
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Vehicle extends Model
{
    protected static string $table = 'vehicles';

    public static function getMakes(): array
    {
        $rows = Database::select("SELECT DISTINCT make FROM vehicles ORDER BY make ASC");
        return array_column($rows, 'make');
    }

    public static function getModels(string $make): array
    {
        $rows = Database::select(
            "SELECT DISTINCT model FROM vehicles WHERE make = :make ORDER BY model ASC",
            ['make' => $make]
        );
        return array_column($rows, 'model');
    }

    public static function getVehiclesByMakeModel(string $make, string $model): array
    {
        return Database::select(
            "SELECT * FROM vehicles WHERE make = :make AND model = :model ORDER BY year_start DESC",
            ['make' => $make, 'model' => $model]
        );
    }

    public static function findMatch(string $make, string $model, int $year): ?array
    {
        return Database::selectOne(
            "SELECT * FROM vehicles WHERE make = :make AND model = :model AND :year BETWEEN year_start AND year_end LIMIT 1",
            ['make' => $make, 'model' => $model, 'year' => $year]
        );
    }

    public static function formatTitle(array $v): string
    {
        return sprintf('%s %s (%d-%d) %s', $v['make'], $v['model'], $v['year_start'], $v['year_end'], $v['engine']);
    }
}

