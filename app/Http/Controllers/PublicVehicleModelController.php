<?php

namespace App\Http\Controllers;

use App\Models\VehicleModel;
use Illuminate\Http\Request;

class PublicVehicleModelController extends Controller
{
    public function show(VehicleModel $vehicleModel)
    {
        if (!request()->hasValidSignature()) {
            abort(401);
        }

        return view('vehicle-model-public', [
            'vehicleModel' => $vehicleModel->load('brand')
        ]);
    }
}
