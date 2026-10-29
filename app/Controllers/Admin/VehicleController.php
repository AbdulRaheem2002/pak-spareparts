<?php
/**
 * PakAutoParts - Admin Vehicle Fitment Controller
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    public function index(Request $request): Response
    {
        $vehicles = Database::select(
            "SELECT v.*, COUNT(pf.product_id) as compatible_parts_count
             FROM vehicles v
             LEFT JOIN product_fitment pf ON v.id = pf.vehicle_id
             GROUP BY v.id
             ORDER BY v.make ASC, v.model ASC, v.year_start DESC"
        );

        return $this->view('admin/vehicles/index', [
            'page_title' => 'Vehicle Database & Compatibility Matrix',
            'vehicles'   => $vehicles,
        ], 'layouts/admin');
    }

    public function save(Request $request): Response
    {
        if (!$this->validateCsrf()) return $this->back();

        $id = (int)$request->post('id', 0);
        $make = trim((string)$request->post('make'));
        $model = trim((string)$request->post('model'));
        $yearStart = (int)$request->post('year_start');
        $yearEnd = (int)$request->post('year_end');
        $engine = trim((string)$request->post('engine'));
        $fuel = $request->post('fuel_type', 'Petrol');

        if (empty($make) || empty($model) || $yearStart <= 0 || $yearEnd <= 0) {
            Session::flash('error', 'Please fill in all vehicle parameters.');
            return $this->back();
        }

        if ($id > 0) {
            Database::update('vehicles', [
                'make'       => $make,
                'model'      => $model,
                'year_start' => $yearStart,
                'year_end'   => $yearEnd,
                'engine'     => $engine,
                'fuel_type'  => $fuel,
            ], 'id = :id', ['id' => $id]);
            Session::flash('success', "Vehicle '{$make} {$model}' updated.");
        } else {
            Database::insert('vehicles', [
                'make'       => $make,
                'model'      => $model,
                'year_start' => $yearStart,
                'year_end'   => $yearEnd,
                'engine'     => $engine,
                'fuel_type'  => $fuel,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            Session::flash('success', "Vehicle '{$make} {$model}' added.");
        }

        return $this->redirect('/admin/vehicles');
    }

    public function delete(Request $request, string $id): Response
    {
        if (!$this->validateCsrf()) return $this->back();

        Database::delete('vehicles', 'id = :id', ['id' => (int)$id]);
        Session::flash('success', 'Vehicle model removed.');
        return $this->redirect('/admin/vehicles');
    }
}

