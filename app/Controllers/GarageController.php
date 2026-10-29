<?php
/**
 * PakAutoParts - Garage Controller
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Vehicle;

class GarageController extends Controller
{
    public function options(Request $request): Response
    {
        $make = trim((string)$request->get('make', ''));
        $model = trim((string)$request->get('model', ''));

        if ($make !== '' && $model !== '') {
            $vehicles = Vehicle::getVehiclesByMakeModel($make, $model);
            return $this->json(['vehicles' => $vehicles]);
        }

        if ($make !== '') {
            $models = Vehicle::getModels($make);
            return $this->json(['models' => $models]);
        }

        $makes = Vehicle::getMakes();
        return $this->json(['makes' => $makes]);
    }

    public function select(Request $request): Response
    {
        $vehicleId = (int)$request->post('vehicle_id');
        $vehicle = Vehicle::find($vehicleId);

        if (!$vehicle) {
            return $this->json(['success' => false, 'message' => 'Vehicle not found'], 404);
        }

        setcookie('my_garage_id', (string)$vehicleId, time() + (86400 * 30), '/', '', false, false);

        return $this->json([
            'success' => true,
            'vehicle' => $vehicle,
            'message' => 'Vehicle successfully saved to My Garage.'
        ]);
    }

    public function clear(Request $request): Response
    {
        setcookie('my_garage_id', '', time() - 3600, '/', '', false, false);
        return $this->json(['success' => true, 'message' => 'Vehicle removed from My Garage.']);
    }
}

