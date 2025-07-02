@php
    use Illuminate\Support\Facades\URL;
    
    $record = $getRecord();
    $modalId = 'qr-modal-model-' . $record->id;
    
    // Generate QR code data without a URL that could cause redirects
    $qrData = json_encode([
        'type' => 'Vehicle',
        'id' => $record->id,
        'name' => $record->name ?? ($record->title ?? 'QR Code')
    ]);
@endphp

<div class="flex justify-center" x-data="{}" x-on:click.stop>
    <button type="button" 
            x-on:click.prevent.stop="
                // Store current URL before opening modal
                const currentUrl = window.location.href;
                
                // Open the modal
                $dispatch('open-modal', { id: '{{ $modalId }}' });
                
                // Set up URL watcher
                const checkUrl = setInterval(() => {
                    if (window.location.href !== currentUrl) {
                        window.history.replaceState(null, null, currentUrl);
                    }
                }, 50);
                
                // Clean up on modal close
                document.addEventListener('modal-closed', function cleanup() {
                    clearInterval(checkUrl);
                    document.removeEventListener('modal-closed', cleanup);
                });
            "
            class="p-1.5 text-gray-500 hover:text-primary-600 transition-colors rounded hover:bg-gray-100"
            title="View QR Code">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
        </svg>
    </button>
</div>

<x-filament::modal 
    :id="$modalId" 
    width="sm"
    x-data="{}"
    x-init="
        // Prevent any URL changes while modal is open
        const originalUrl = window.location.href;
        const originalState = window.history.state;
        
        // Block any URL changes
        const blockUrlChanges = () => {
            window.history.replaceState(originalState, '', originalUrl);
            return false;
        };
        
        // Add event listeners to prevent URL changes
        window.addEventListener('popstate', blockUrlChanges);
        
        // Clean up on modal close
        return () => {
            window.removeEventListener('popstate', blockUrlChanges);
        };
    "
    x-on:click.stop
    x-on:keydown.escape.prevent.stop="$dispatch('close-modal', { id: '{{ $modalId }}' })"
    x-on:keydown.enter.prevent.stop
    x-on:keydown.space.prevent.stop
    x-on:keydown.tab.prevent.stop="$event.shiftKey ? $focus.wrap().previous() : $focus.wrap().next()"
    x-on:keydown.shift.tab.prevent.stop="$focus.wrap().previous()"
    x-on:click.away.prevent="
        // Dispatch close event
        $dispatch('close-modal', { id: '{{ $modalId }}' });
        // Reset URL to the current path
        window.history.replaceState(null, null, window.location.pathname);
        // Dispatch custom event to clean up URL watchers
        document.dispatchEvent(new Event('modal-closed'));
    "
    x-on:keydown.escape.prevent.stop="
        $dispatch('close-modal', { id: '{{ $modalId }}' });
        window.history.replaceState(null, null, window.location.pathname);
        document.dispatchEvent(new Event('modal-closed'));
    ">
    <x-slot name="heading">
        <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd" />
                <path d="M8 6h4v1H8V6z" />
            </svg>
            <span>Vehicle QR Codess</span>
        </div>
    </x-slot>

    <div class="p-4">
        <div class="flex justify-center mb-4">
            <img 
                src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=10&data={{ urlencode($qrData) }}" 
                alt="QR Code"
                class="border border-gray-200 rounded"
                x-on:click.stop
            >
        </div>
        
        <div class="mt-4 text-sm text-gray-600 text-center">
            <p>Scan this code to identify this vehicle</p>
            <div class="mt-2 p-2 bg-gray-100 rounded text-xs break-all">
                <div class="font-medium">{{ $record->name ?? 'Vehicle' }}</div>
                <div class="text-gray-500 text-2xs mt-1">ID: {{ $record->id }}</div>
            </div>
        </div>
    </div>
</x-filament::modal>
