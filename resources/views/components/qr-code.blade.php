@php
    use Illuminate\Support\Facades\URL;
    
    // Get the record and generate the URL
    $record = $getRecord();
    $url = URL::signedRoute('vehicle.public.view', ['vehicle' => $record->id]);
    
    // Generate SVG with QR code using Google Charts API
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50">';
    $svg .= '<rect width="100%" height="100%" fill="#FFFFFF"/>';
    $svg .= '<image xlink:href="https://api.qrserver.com/v1/create-qr-code/?size=50x50&margin=0&data=' . urlencode($url) . '" width="50" height="50"/>';
    $svg .= '</svg>';
    
    $base64Svg = 'data:image/svg+xml;base64,' . base64_encode($svg);
@endphp

<div x-data="{ showTooltip: false }" class="relative inline-block">
    <img 
        src="{{ $base64Svg }}" 
        alt="QR Code for {{ $url }}"
        class="h-10 w-10"
        x-on:mouseover="showTooltip = true"
        x-on:mouseout="showTooltip = false"
    >
    <div 
        x-show="showTooltip"
        class="absolute z-10 w-32 p-2 text-xs text-white bg-gray-800 rounded-md bottom-full left-1/2 transform -translate-x-1/2 mb-1"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
    >
        Scan to view vehicle specs
    </div>
</div>
