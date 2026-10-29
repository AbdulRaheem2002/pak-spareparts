<?php
/**
 * PakAutoParts - Audit Log Model
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class AuditLog extends Model
{
    protected static string $table = 'audit_logs';
}

