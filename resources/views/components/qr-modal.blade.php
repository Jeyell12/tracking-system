<div class="p-4 text-center">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Vehicle QR Code</h3>
    
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
    
    <div class="mt-6 flex justify-end">
        <button type="button" 
                x-on:click="$dispatch('close')"
                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Close
        </button>
    </div>
</div>
