<x-filament-panels::page>
    <div class="space-y-6">

         @foreach ($this->priceBooks as $book)
            <div class="rounded-lg border bg-white dark:bg-gray-900">
                <div class="flex items-center justify-between p-4 border-b">
                    <div class="font-semibold">
                        {{ $book->name }}
                    </div>
                    <div>
                    <x-filament::button
                        color="primary"
                        icon="heroicon-m-plus"
                        wire:click="openNewPriceModal({{ $book->id }})"
                        x-tooltip="'Add a new price to this book'"
                    >
                        New Price
                    </x-filament::button>


    <x-filament::button
        color="warning"
        icon="heroicon-m-pencil"
        wire:click="editPriceBook({{ $book->id }})"
        x-tooltip="'Edit this price book'"
    >
    </x-filament::button>




                    <x-filament::button
                        color="danger"
                        icon="heroicon-m-trash"
                        wire:click="deletePriceBook({{ $book->id }})"
                        wire:confirm="Are you sure you want to delete this price book and all its prices?"
                        x-tooltip="'Delete this price book and all prices'"
                    >
                    </x-filament::button>


                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-2 text-left">Day of Week</th>
                                <th class="px-4 py-2 text-left">Time</th>
                                <th class="px-4 py-2 text-left">Per Hour</th>
                                <th class="px-4 py-2 text-left">Reference Number (Hour)</th>
                                <th class="px-4 py-2 text-left">Per Km</th>
                                <th class="px-4 py-2 text-left">Reference Number</th>
                                <th class="px-4 py-2 text-left">Effective Date</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($book->priceBookDetails as $detail)
                                <tr class="border-t">
                                    <td class="px-4 py-2">{{ $detail->day_of_week }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($detail->start_time)->format('h:i A') }}
                                            -
                                            {{ \Carbon\Carbon::parse($detail->end_time)->format('h:i A') }}
                                        </td>

                                    <td class="px-4 py-2">${{ number_format($detail->per_hour, 2) }}</td>
                                    <td class="px-4 py-2">{{ $detail->ref_hour }}</td>
                                    <td class="px-4 py-2">${{ number_format($detail->per_km, 2) }}</td>
                                    <td class="px-4 py-2">{{ $detail->ref_km }}</td>
                                    <td class="px-4 py-2">{{ $detail->effective_date->format('d-m-Y') }}</td>
                                    <td class="px-4 py-2">
                                        <div class="flex space-x-2">
                                            <x-filament::icon-button
                                                icon="heroicon-m-document-duplicate"
                                                color="success"
                                                size="sm"
                                                wire:click="copyPriceRow({{ $detail->id }})"
                                                x-tooltip="'Copy Price'"
                                                style="margin-right: 10px"
                                            />

                                            <x-filament::icon-button
                                                icon="heroicon-m-pencil-square"
                                                color="warning"
                                                size="sm"
                                                wire:click.stop.prevent="openEditPriceModal({{ $detail->id }})"
                                                x-tooltip="'Edit Price'"
                                                style="margin-right: 10px"
                                            />

                                            <x-filament::icon-button
                                                icon="heroicon-m-trash"
                                                color="danger"
                                                size="sm"
                                                wire:click="deletePriceRow({{ $detail->id }})"
                                                wire:confirm="Are you sure you want to delete this price?"
                                                x-tooltip="'Delete Price'"
                                            />

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">No Data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
        </div>

    <!-- New Price Modal -->
<x-filament::modal id="new-price-modal" :close-by-clicking-away="false">

        <x-slot name="heading">
            New Price
        </x-slot>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Day of Week</label>
                    <select wire:model="priceData.day_of_week" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="">Select Day</option>
                        <option value="Weekdays - I">Weekdays - I</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                        <option value="Public Holidays">Public Holidays</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Start Time</label>
                    <input type="time" wire:model="priceData.start_time" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">End Time</label>
                    <input type="time" wire:model="priceData.end_time" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                                        <!-- Reference Number (Hour) -->
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Reference Number (Hour)
                                    </label>
                                    <input
                                        type="text"
                                        wire:model.defer="priceData.ref_hour"
                                        wire:change="fetchPerHourFromRefValue('priceData', $event.target.value)"
                                        wire:keydown.enter="fetchPerHourFromRefValue('priceData', $event.target.value)"
                                        class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2
                                            bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                        placeholder="Enter exact Reference Number (e.g. 01_023_0120_1_1)"
                                    />
                                </div>

                                <!-- Per Hour - Create (readonly) -->
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Per Hour</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        wire:model="priceData.per_hour"
                                        readonly
                                        class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2
                                            bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                        placeholder="Auto-filled from prices table"
                                    />
                                </div>



                                    <!-- Reference Number (KM) - Create -->
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Reference Number (KM)
                                        </label>
                                        <input
                                            type="text"
                                            wire:model.defer="priceData.ref_km"
                                            wire:change="fetchPerKmFromRefValue('priceData', $event.target.value)"
                                            wire:keydown.enter="fetchPerKmFromRefValue('priceData', $event.target.value)"
                                            class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2
                                                bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                            placeholder="Enter exact Reference Number (e.g. 01_023_0120_1_1)"
                                        />
                                    </div>

                                    <!-- Per KM - Create (readonly) -->
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Per KM</label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            wire:model="priceData.per_km"
                                            readonly
                                            class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2
                                                bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                            placeholder="Auto-filled from prices table"
                                        />
                                    </div>

                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Effective Date</label>
                    <input id="create-input" type="date" wire:model="priceData.effective_date" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex justify-end space-x-2">
                <x-filament::button
                    color="gray"
                    wire:click="closeNewPriceModal"
                >
                    Cancel
                </x-filament::button>
                <x-filament::button
                    color="primary"
                    wire:click="savePriceRow"
                >
                    Save
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament::modal>

    <!-- Edit Price Modal -->
<x-filament::modal id="edit-price-modal" :close-by-clicking-away="false">
    <x-slot name="heading">Edit Price</x-slot>

    <div class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Day of Week</label>
                <select wire:model="editingPriceData.day_of_week"
                        class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Select Day</option>
                    <option value="Weekdays - I">Weekdays - I</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                    <option value="Public Holidays">Public Holidays</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Start Time</label>
                <input type="time" wire:model="editingPriceData.start_time"
                       class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">End Time</label>
                <input type="time" wire:model="editingPriceData.end_time"
                       class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
                            <!-- Reference Number (Hour) - Edit -->
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Reference Number (Hour)
                                </label>
                                <input
                                    type="text"
                                    wire:model.defer="editingPriceData.ref_hour"
                                    wire:change="fetchPerHourFromRefValue('editingPriceData', $event.target.value)"
                                    wire:keydown.enter="fetchPerHourFromRefValue('editingPriceData', $event.target.value)"
                                    class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2
                                        bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                    placeholder="Enter exact Reference Number (e.g. 01_023_0120_1_1)"
                                />
                            </div>

                            <!-- Per Hour - Edit (readonly) -->
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Per Hour</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    wire:model="editingPriceData.per_hour"
                                    readonly
                                    class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2
                                        bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                    placeholder="Auto-filled from prices table"
                                />
                            </div>



                                <!-- Reference Number (KM) - Edit -->
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Reference Number (KM)
                                    </label>
                                    <input
                                        type="text"
                                        wire:model.defer="editingPriceData.ref_km"
                                        wire:change="fetchPerKmFromRefValue('editingPriceData', $event.target.value)"
                                        wire:keydown.enter="fetchPerKmFromRefValue('editingPriceData', $event.target.value)"
                                        class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2
                                            bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                        placeholder="Enter exact Reference Number (e.g. 01_023_0120_1_1)"
                                    />
                                </div>

                                <!-- Per KM - Edit (readonly) -->
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Per KM</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        wire:model="editingPriceData.per_km"
                                        readonly
                                        class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2
                                            bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                        placeholder="Auto-filled from prices table"
                                    />
                                </div>


            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Effective Date</label>
                <input id="edit-input" type="date" wire:model="editingPriceData.effective_date"
                       class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
        </div>
    </div>

    <x-slot name="footer">
        <div class="flex justify-end space-x-2">
            <x-filament::button color="gray" wire:click="closeEditPriceModal">
                Cancel
            </x-filament::button>

            <x-filament::button color="primary" wire:click="updatePriceRow">
                Update
            </x-filament::button>
        </div>
    </x-slot>
</x-filament::modal>

<x-filament::modal id="editPriceBookModal">
    <x-slot name="heading">
        Edit Price Book
    </x-slot>

    <div class="space-y-4">
        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
            <input
                type="text"
                wire:model.defer="editForm.name"
                required
                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
            >
        </div>

        {{-- External ID & Xero Invoice Prefix (side by side) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">External ID</label>
                <input
                    type="text"
                    wire:model.defer="editForm.external_id"
                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Xero Invoice Prefix</label>
                <input
                    type="text"
                    wire:model.defer="editForm.xero_invoice_prefix"
                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
                >
            </div>
        </div>

        {{-- Checkboxes --}}
        <div class="space-y-2">
            <label class="flex items-center gap-2">
                <input type="checkbox" wire:model.defer="editForm.fixed_price" class="rounded border-gray-300 dark:border-gray-600">
                <span class="text-gray-700 dark:text-gray-300">Fixed Price Only</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" wire:model.defer="editForm.provider_travel" class="rounded border-gray-300 dark:border-gray-600">
                <span class="text-gray-700 dark:text-gray-300">Provider Travel</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" wire:model.defer="editForm.national_pricing" class="rounded border-gray-300 dark:border-gray-600">
                <span class="text-gray-700 dark:text-gray-300">National Pricing</span>
            </label>
        </div>
    </div>

    <x-slot name="footer">
        <x-filament::button wire:click="updatePriceBook" color="primary">
            Update
        </x-filament::button>
    </x-slot>
</x-filament::modal>

<script>
        document.addEventListener('DOMContentLoaded', function () {
        if (!window.initCustomDatePicker) return;

        ['create-input','edit-input'].forEach(function (id) {
            window.initCustomDatePicker(id);
        });
    });
</script>

</x-filament-panels::page>
