<div class="container p-6 mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
            Manage Monitors
        </h1>

        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}"
                class="inline-block px-3 py-2 text-gray-700 transition bg-white border border-gray-300 rounded shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700">
                &larr; Back to Dashboard
            </a>

            <button wire:click="create()"
                class="px-4 py-2 text-white bg-blue-600 rounded shadow hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500">
                + Add New Monitor
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 border-l-4 border-green-500" role="alert">
            <p>{{ session('message') }}</p>
        </div>
    @endif

    <div class="my-6 overflow-x-auto bg-white rounded shadow-md">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="text-sm leading-normal text-gray-600 uppercase bg-gray-200">
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-left">Type</th>
                    <th class="px-6 py-3 text-left">Target (URL/IP)</th>
                    <th class="px-6 py-3 text-center">Active</th>
                    <th class="px-6 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm font-light text-gray-600">
                @foreach ($monitors as $monitor)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="px-6 py-3 font-medium text-left whitespace-nowrap">{{ $monitor->name }}</td>
                        <td class="px-6 py-3 text-left capitalize">{{ $monitor->type }}</td>
                        <td class="px-6 py-3 text-left">
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-500">App: {{ $monitor->url ?? $monitor->host }}</span>
                                @if ($monitor->db_host)
                                    <span class="text-xs text-gray-500">DB: {{ $monitor->db_host }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <span
                                class="{{ $monitor->is_active ? 'bg-green-200 text-green-600' : 'bg-red-200 text-red-600' }} py-1 px-3 rounded-full text-xs">
                                {{ $monitor->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <div class="flex justify-center space-x-2 item-center">
                                <button wire:click="edit({{ $monitor->id }})"
                                    class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $monitor->id }})" wire:confirm="Are you sure?"
                                    class="w-4 mr-2 transform hover:text-red-500 hover:scale-110">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form>
                        <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                            <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                {{ $monitor_id ? 'Edit Monitor' : 'Create New Monitor' }}
                            </h3>

                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block mb-2 text-sm font-bold text-gray-700">Service Name</label>
                                    <input type="text" wire:model="name"
                                        class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">
                                    @error('name')
                                        <span class="text-xs text-red-500">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-bold text-gray-700">Type</label>
                                    <select wire:model.live="type"
                                        class="w-full px-3 py-2 text-gray-700 border rounded shadow">
                                        <option value="web">Web Application (HTTP)</option>
                                        <option value="desktop">Server / Desktop App</option>
                                    </select>
                                </div>

                                {{-- @if ($type === 'web') --}}
                                <div>
                                    <label class="block mb-2 text-sm font-bold text-gray-700">Web URL</label>
                                    <input type="text" wire:model="url" placeholder="https://..."
                                        class="w-full px-3 py-2 text-gray-700 border rounded shadow appearance-none">
                                    @error('url')
                                        <span class="text-xs text-red-500">{{ $message }}</span>
                                    @enderror
                                </div>
                                {{-- @endif --}}

                                <div>
                                    <label class="block mb-2 text-sm font-bold text-gray-700">Server Host / IP</label>
                                    <input type="text" wire:model="host" placeholder="192.168.x.x or domain"
                                        class="w-full px-3 py-2 text-gray-700 border rounded shadow appearance-none">
                                    @error('host')
                                        <span class="text-xs text-red-500">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- @if ($type === 'desktop') --}}
                                <div>
                                    <label class="block mb-2 text-sm font-bold text-gray-700">Service Port
                                        (RDP/App)</label>
                                    <input type="number" wire:model="port" placeholder="e.g 3389"
                                        class="w-full px-3 py-2 text-gray-700 border rounded shadow appearance-none">
                                </div>
                                {{-- @endif --}}

                                <div class="pt-4 mt-2 border-t">
                                    <h4 class="mb-2 text-sm font-semibold text-gray-500">Database Check (Optional)</h4>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block mb-2 text-xs font-bold text-gray-700">DB Host</label>
                                            <input type="text" wire:model="db_host" placeholder="Same as Host?"
                                                class="w-full px-3 py-2 text-gray-700 border rounded shadow appearance-none">
                                        </div>
                                        <div>
                                            <label class="block mb-2 text-xs font-bold text-gray-700">DB Port</label>
                                            <input type="number" wire:model="db_port"
                                                class="w-full px-3 py-2 text-gray-700 border rounded shadow appearance-none">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" wire:model="is_active"
                                            class="w-5 h-5 text-blue-600 form-checkbox">
                                        <span class="ml-2 text-gray-700">Monitor Active?</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" wire:click="store()"
                                class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-white bg-blue-600 border border-gray-300 rounded-md shadow-sm hover:bg-blue-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Save
                            </button>
                            <button type="button" wire:click="closeModal()"
                                class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
