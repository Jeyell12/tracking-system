<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $vehicleModel->brand->name }} {{ $vehicleModel->name }} - Vehicle Model</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-2xl">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ $vehicleModel->brand->name }} {{ $vehicleModel->name }}
                    </h1>
                    <p class="text-gray-600">Vehicle Model Details</p>
                </div>
                <div class="bg-gray-100 p-3 rounded-lg">
                    <img 
                        src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&margin=10&data={{ urlencode(url()->current()) }}" 
                        alt="QR Code"
                        class="w-24 h-24"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-semibold mb-2 text-gray-700">Model Information</h2>
                    <dl class="space-y-2">
                        <div class="flex">
                            <dt class="w-1/3 text-gray-600">Brand:</dt>
                            <dd class="w-2/3 font-medium">{{ $vehicleModel->brand->name }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="w-1/3 text-gray-600">Model:</dt>
                            <dd class="w-2/3 font-medium">{{ $vehicleModel->name }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="w-1/3 text-gray-600">Type:</dt>
                            <dd class="w-2/3 font-medium">{{ $vehicleModel->vehicle_type }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="w-1/3 text-gray-600">Transmission:</dt>
                            <dd class="w-2/3 font-medium">{{ $vehicleModel->transmission_type }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="w-1/3 text-gray-600">Years:</dt>
                            <dd class="w-2/3 font-medium">{{ implode(', ', $vehicleModel->years) }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="w-1/3 text-gray-600">Status:</dt>
                            <dd class="w-2/3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $vehicleModel->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $vehicleModel->status ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h2 class="text-lg font-semibold mb-2 text-gray-700">Vehicles</h2>
                    @if($vehicleModel->vehicles->count() > 0)
                        <div class="space-y-2">
                            @foreach($vehicleModel->vehicles->take(5) as $vehicle)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <div class="font-medium">{{ $vehicle->license_plate }}</div>
                                    <div class="text-sm text-gray-600">
                                        {{ $vehicle->year }} • {{ $vehicle->color }}
                                        @if($vehicle->vin)
                                            <div class="text-xs text-gray-500 mt-1">VIN: {{ $vehicle->vin }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            @if($vehicleModel->vehicles->count() > 5)
                                <div class="text-center text-sm text-gray-500 mt-2">
                                    +{{ $vehicleModel->vehicles->count() - 5 }} more vehicles
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500">No vehicles found for this model.</p>
                    @endif
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500 text-center">
                    This is a public view for {{ $vehicleModel->brand->name }} {{ $vehicleModel->name }}. 
                    The information on this page is read-only.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
