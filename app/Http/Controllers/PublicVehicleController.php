<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class PublicVehicleController extends Controller
{
    public function show(Vehicle $vehicle)
    {
        // Check if we're accessing via QR code (no signature required)
        $isQrAccess = request()->query('qr', false);
        
        // If not accessing via QR code, require a valid signature
        if (!$isQrAccess && !request()->hasValidSignature()) {
            abort(401, 'Invalid or expired link');
        }

        // Eager load relationships and maintenance requests
        $vehicle->load([
            'brand',
            'model',
            'maintenanceRequests' => function($query) {
                $query->latest('requested_date');
            }
        ]);

        return view('vehicles.public-show', [
            'vehicle' => $vehicle
        ]);
    }
    
    public function generateQrUrl(Vehicle $vehicle)
    {
        return response()->json([
            'url' => route('vehicles.public.show', ['vehicle' => $vehicle, 'qr' => true])
        ]);
    }
}
