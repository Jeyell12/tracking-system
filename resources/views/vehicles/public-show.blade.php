<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Details - {{ $vehicle->vin }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <div class="max-w-6xl mx-auto p-4 sm:p-6">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-t-lg p-6 shadow-lg">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold">{{ $vehicle->year }} {{ $vehicle->brand?->name ?? '' }} {{ $vehicle->model?->name ?? '' }}</h1>
                        <div class="flex flex-wrap items-center mt-2 space-x-4 text-sm text-blue-100">
                            <span><i class="fas fa-barcode mr-1"></i> {{ $vehicle->vin }}</span>
                            <span><i class="fas fa-tag mr-1"></i> {{ $vehicle->license_plate ?? 'N/A' }}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $vehicle->status === 'active' ? 'bg-green-200 text-green-800' : 
                                   ($vehicle->status === 'maintenance' ? 'bg-yellow-200 text-yellow-800' : 'bg-red-200 text-red-800') }}">
                                {{ ucfirst($vehicle->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="{{ route('vehicles.public.show', ['vehicle' => $vehicle, 'print' => 'pdf']) }}" 
                           class="inline-flex items-center px-4 py-2 bg-white text-blue-700 rounded-lg hover:bg-blue-50 transition-colors">
                            <i class="fas fa-print mr-2"></i> Print Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Vehicle Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <!-- Vehicle Details Card -->
                <div class="bg-white rounded-lg shadow-md p-6 col-span-2">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2 flex items-center">
                        <i class="fas fa-car-side text-blue-600 mr-2"></i>
                        Vehicle Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Make</h3>
                                <p class="mt-1 text-gray-900">{{ $vehicle->brand?->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Model</h3>
                                <p class="mt-1 text-gray-900">{{ $vehicle->model?->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Year</h3>
                                <p class="mt-1 text-gray-900">{{ $vehicle->year ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Color</h3>
                                <p class="mt-1 text-gray-900">{{ $vehicle->color ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Vehicle Type</h3>
                                <p class="mt-1 text-gray-900">{{ $vehicle->vehicle_type ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Mileage</h3>
                                <p class="mt-1 text-gray-900">{{ number_format($vehicle->current_mileage) ?? 'N/A' }} miles</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Fuel Type</h3>
                                <p class="mt-1 text-gray-900">{{ $vehicle->fuel_type ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Transmission</h3>
                                <p class="mt-1 text-gray-900">{{ $vehicle->transmission_type ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Status Card -->
                <div class="space-y-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2 flex items-center">
                            <i class="fas fa-tools text-blue-600 mr-2"></i>
                            Service Status
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Last Service</h3>
                                <p class="mt-1 text-gray-900">
                                    {{ $vehicle->last_service_date ? $vehicle->last_service_date->format('M d, Y') : 'No service record' }}
                                </p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Next Service Due</h3>
                                <p class="mt-1 text-gray-900">
                                    {{ $vehicle->next_service_due_date ? $vehicle->next_service_due_date->format('M d, Y') : 'Not scheduled' }}
                                </p>
                            </div>
                            <div class="pt-4 border-t">
                                <h3 class="text-sm font-medium text-gray-500">Insurance</h3>
                                <p class="mt-1 text-gray-900">
                                    {{ $vehicle->insurance_provider ?? 'N/A' }}
                                    @if($vehicle->insurance_expiry_date)
                                        <span class="block text-sm text-gray-500">Expires: {{ $vehicle->insurance_expiry_date->format('M d, Y') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2 flex items-center">
                            <i class="fas fa-bolt text-blue-600 mr-2"></i>
                            Quick Actions
                        </h2>
                        <div class="space-y-2">
                            <a href="#maintenance-history" class="flex items-center p-2 text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                                <i class="fas fa-history text-blue-500 w-6"></i>
                                <span>View Maintenance History</span>
                            </a>
                            <a href="tel:{{ config('app.contact_phone', '1-800-123-4567') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                                <i class="fas fa-phone-alt text-green-500 w-6"></i>
                                <span>Contact Service Center</span>
                            </a>
                            <a href="{{ route('maintenance-request.create', ['vehicle_id' => $vehicle->id]) }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                                <i class="fas fa-tools text-yellow-500 w-6"></i>
                                <span>Request Service</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maintenance History -->
            <div id="maintenance-history" class="mt-8 bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-6 text-gray-800 flex items-center">
                        <i class="fas fa-history text-blue-600 mr-2"></i>
                        Maintenance History
                    </h2>
                    
                    @if($vehicle->maintenanceRequests && $vehicle->maintenanceRequests->count() > 0)
                        <div class="space-y-6">
                            @foreach($vehicle->maintenanceRequests->sortByDesc('requested_date') as $request)
                                <div class="border-l-4 border-blue-500 pl-4 py-2">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-medium text-gray-900">{{ $request->title }}</h3>
                                            <p class="text-sm text-gray-600">{{ $request->description }}</p>
                                            <div class="mt-1 flex items-center text-sm text-gray-500">
                                                <span>Requested: {{ $request->requested_date->format('M d, Y') }}</span>
                                                @if($request->completed_date)
                                                    <span class="mx-2">•</span>
                                                    <span>Completed: {{ $request->completed_date->format('M d, Y') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $request->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                               ($request->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ str_replace('_', ' ', ucfirst($request->status)) }}
                                        </span>
                                    </div>
                                    @if($request->notes)
                                        <div class="mt-2 p-3 bg-gray-50 rounded-md text-sm text-gray-600">
                                            <span class="font-medium">Notes:</span> {{ $request->notes }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-clipboard-list text-4xl mb-2 text-gray-300"></i>
                            <p>No maintenance records found for this vehicle.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-sm text-gray-500">
                <p>Scanned on {{ now()->format('F j, Y \a\t g:i A') }}</p>
                <p class="mt-1">© {{ date('Y') }} {{ config('app.name', 'Vehicle Management System') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    @if($vehicle->notes)
                    <div class="mt-6">
                        <h2 class="text-xl font-semibold mb-2">Additional Notes</h2>
                        <p class="bg-gray-50 p-4 rounded">{{ $vehicle->notes }}</p>
                    </div>
                    @endif
                </div>
                
                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500 text-center">
                        This link will expire in 30 days. Generated on {{ now()->format('F j, Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
