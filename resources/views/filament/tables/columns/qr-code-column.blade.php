@php
    $record = $getRecord();
    $modalId = 'qr-modal-' . $record->getKey();
    $size = $getSize();
    
    // Generate a unique identifier for this record (using route if available, otherwise use ID)
    $identifier = method_exists($record, 'getRouteKey') ? $record->getRouteKey() : $record->getKey();
    $qrData = json_encode([
        'type' => class_basename($record),
        'id' => $identifier,
        'name' => $record->name ?? ($record->title ?? 'QR Code')
    ]);
@endphp

<div class="w-full flex items-center justify-center">
    <div class="inline-flex items-center justify-center" x-data="{}" x-on:click.stop>
        <button type="button" 
                x-on:click.prevent.stop="
                    $event.preventDefault();
                    $event.stopPropagation();
                    $dispatch('open-modal', { id: '{{ $modalId }}' });
                "
                class="p-2 text-gray-500 hover:text-primary-600 transition-colors rounded-full hover:bg-gray-100 flex items-center justify-center"
                title="View QR Code"
                style="width: 36px; height: 36px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
            </svg>
        </button>
    </div>
</div>

<x-filament::modal 
    :id="$modalId" 
    width="md"
    x-data="{}"
    x-init="
        $nextTick(() => {
            // Remove any click handlers that might cause redirects
            const modal = document.getElementById('{{ $modalId }}');
            if (modal) {
                modal.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }, true);
            }
        })
    "
    x-on:click.stop
    x-on:keydown.escape.prevent.stop="$dispatch('close-modal', { id: '{{ $modalId }}' })"
    x-on:keydown.enter.prevent.stop
    x-on:keydown.space.prevent.stop
    x-on:keydown.tab.prevent.stop="$event.shiftKey ? $focus.wrap().previous() : $focus.wrap().next()"
    x-on:keydown.shift.tab.prevent.stop="$focus.wrap().previous()"
    x-on:click.away="$dispatch('close-modal', { id: '{{ $modalId }}' })">
    <x-slot name="heading">
        <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd" />
                <path d="M8 6h4v1H8V6z" />
            </svg>
            <span>{{ class_basename($record) }} QR Code</span>
        </div>
    </x-slot>

    <div class="p-6">
        <div class="flex flex-col items-center space-y-6">
            <!-- QR Code Container -->
            <div class="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
                <img 
                    src="https://api.qrserver.com/v1/create-qr-code/?size={{ $size }}x{{ $size }}&margin=20&data={{ urlencode($qrData) }}" 
                    alt="QR Code"
                    class="w-full h-auto max-w-xs mx-auto border-2 border-gray-200 rounded-lg"
                    style="min-width: 300px; min-height: 300px;"
                >
            </div>
            
            <!-- Vehicle Info -->
            <div class="w-full max-w-xs text-center">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Vehicle Identification</h3>
                <div class="space-y-1 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span class="font-medium">VIN:</span>
                        <span class="text-gray-700">{{ $record->vin ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">License Plate:</span>
                        <span class="text-gray-700">{{ $record->license_plate ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Make/Model:</span>
                        <span class="text-gray-700">{{ ($record->brand?->name ?? 'N/A') . ' ' . ($record->model?->name ?? '') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium">Year:</span>
                        <span class="text-gray-700">{{ $record->year ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-8 text-center">
            <p class="text-sm font-medium text-gray-600 mb-1">Scan this QR code to view vehicle details</p>
            <p class="text-xs text-gray-500">Point your device's camera at the QR code</p>
        </div>
    </div>
</x-filament::modal>
