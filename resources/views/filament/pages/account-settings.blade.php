<x-filament::page>
    <div class="space-y-6">
        <div class="space-y-6">
            <x-filament::card>
                <form wire:submit="save" class="space-y-6">
                    <div class="space-y-6">
                        <div class="space-y-6">
                            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                                        {{ __('Profile Information') }}
                                    </h3>
                                    <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('Update your account\'s profile information and email address.') }}
                                    </p>
                                </div>
                                <div class="px-4 py-5 sm:p-6">
                                    <div class="space-y-6">
                                        <div>
                                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('Name') }}
                                            </label>
                                            <div class="mt-1">
                                                <input type="text" id="name" wire:model="name" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            </div>
                                        </div>

                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('Email') }}
                                            </label>
                                            <div class="mt-1">
                                                <input type="email" id="email" wire:model="email" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                                        {{ __('Password') }}
                                    </h3>
                                    <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('Ensure your account is using a long, random password to stay secure.') }}
                                    </p>
                                </div>
                                <div class="px-4 py-5 sm:p-6">
                                    <div class="space-y-6">
                                        <div>
                                            <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('Current Password') }}
                                            </label>
                                            <div class="mt-1">
                                                <input type="password" id="current_password" wire:model="current_password" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            </div>
                                        </div>

                                        <div>
                                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('New Password') }}
                                            </label>
                                            <div class="mt-1">
                                                <input type="password" id="password" wire:model="password" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            </div>
                                        </div>

                                        <div>
                                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('Confirm New Password') }}
                                            </label>
                                            <div class="mt-1">
                                                <input type="password" id="password_confirmation" wire:model="password_confirmation" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (session()->has('error'))
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        @endif

                        <div class="flex justify-end items-center gap-4">
                            <x-filament::button type="submit" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">{{ __('Save') }}</span>
                                <span wire:loading wire:target="save" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ __('Saving...') }}
                                </span>
                            </x-filament::button>
                            @if (session('status') === 'profile-updated')
                                <p
                                    x-data="{ show: true }"
                                    x-show="show"
                                    x-transition
                                    x-init="setTimeout(() => show = false, 2000)"
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                >
                                    {{ __('Saved.') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </form>
            </x-filament::card>
        </div>
    </div>
</x-filament::page>
