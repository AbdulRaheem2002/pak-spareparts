<?php
/**
 * PakAutoParts - Admin Settings & Backup Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index(Request $request): Response
    {
        $settings = Setting::loadAll();

        return $this->view('admin/settings/index', [
            'page_title' => 'Store & Business Configuration',
            'settings'   => $settings,
        ], 'layouts/admin');
    }

    public function save(Request $request): Response
    {
        if (!$this->validateCsrf()) return $this->back();

        $keys = [
            'site_name',
            'site_tagline',
            'contact_phone',
            'contact_whatsapp',
            'contact_email',
            'contact_address',
            'announcement_banner',
            'bank_name',
            'bank_title',
            'bank_account_no',
            'bank_iban',
            'jazzcash_title',
            'jazzcash_no',
            'easypaisa_title',
            'easypaisa_no',
            'default_shipping_fee',
            'cod_fee',
            'free_shipping_min',
        ];

        foreach ($keys as $k) {
            if ($request->has($k)) {
                Setting::set($k, (string)$request->post($k));
            }
        }

        Session::flash('success', 'Store settings updated successfully.');
        return $this->redirect('/admin/settings');
    }

    /**
     * One-Click Database Backup in pure PHP (no shell_exec/mysqldump required!)
     */
    public function backupDb(Request $request): void
    {
        $db = Database::connect();
        $tables = [];
        $stmt = $db->query("SHOW TABLES");
        while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $sqlDump = "-- ==========================================================\n"
                 . "-- PakAutoParts Database Backup\n"
                 . "-- Date: " . date('Y-m-d H:i:s') . "\n"
                 . "-- ==========================================================\n\n"
                 . "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($tables as $table) {
            // Get CREATE TABLE
            $createRow = $db->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_NUM);
            $sqlDump .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sqlDump .= $createRow[1] . ";\n\n";

            // Get Rows
            $rows = $db->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $cols = array_keys($rows[0]);
                $colNames = '`' . implode('`, `', $cols) . '`';

                foreach ($rows as $r) {
                    $vals = [];
                    foreach ($r as $val) {
                        if ($val === null) {
                            $vals[] = 'NULL';
                        } else {
                            $vals[] = $db->quote((string)$val);
                        }
                    }
                    $sqlDump .= "INSERT INTO `{$table}` ({$colNames}) VALUES (" . implode(', ', $vals) . ");\n";
                }
                $sqlDump .= "\n";
            }
        }

        $sqlDump .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        $filename = 'pakautoparts_backup_' . date('Y-m-d_His') . '.sql';

        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($sqlDump));

        echo $sqlDump;
        exit;
    }
}

