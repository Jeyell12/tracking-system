@props(['record'])

@php
    use Illuminate\Support\Facades\URL;
    
    $record = is_callable($record) ? $record() : $record;
    $url = URL::signedRoute('vehicle.public.view', ['vehicle' => $record->id]);
    $modalId = 'qr-modal-' . $record->id;
@endphp

<div class="flex justify-center">
    <button type="button" 
            x-data="{}"
            x-on:click="
                $event.preventDefault();
                $event.stopPropagation();
                $dispatch('open-modal', { id: '{{ $modalId }}' })
            "
            class="p-1.5 text-gray-500 hover:text-primary-600 transition-colors rounded hover:bg-gray-100"
            title="View Vehicle Specs">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
            </svg>
        </button>
    </div>

<x-filament::modal :id="$modalId" width="md" x-on:click.stop>
    <x-slot name="heading">
        Vehicle QR Code
    </x-slot>

    <div class="p-4">
        <div class="flex justify-center mb-4">
            <img 
                src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=10&data={{ urlencode($url) }}" 
                alt="QR Code"
                class="border border-gray-200 rounded"
            >
        </div>
        
        <div class="mt-4 text-sm text-gray-600">
            <p>Scan this code to view vehicle details</p>
            <div class="mt-2 p-2 bg-gray-100 rounded text-xs break-all">
                {{ $url }}
            </div>
        </div>
    </div>
</x-filament::modal>
