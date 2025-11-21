<x-filament-panels::page>
    <div class="space-y-6">

@foreach ($this->payGroups as $book)
            <div class="rounded-lg border bg-white dark:bg-gray-900">
                <div class="flex items-center justify-between p-4 border-b">
                    <div class="font-semibold">
                        {{ $book->name }}
                    </div>
                    <div>
                    <x-filament::button
                        color="primary"
                        icon="heroicon-m-plus"
                        wire:click="openNewPayItem({{ $book->id }})"
                        x-tooltip="'Add a new pay item to this pay group'"
                    >
                        New Pay Item
                    </x-filament::button>


                    <x-filament::button
                        color="warning"
                        icon="heroicon-m-pencil"
                        wire:click="editPayGroup({{ $book->id }})"
                        x-tooltip="'Edit this pay group'"
                    >
                    </x-filament::button>




                    <x-filament::button
                        color="danger"
                        icon="heroicon-m-trash"
                        wire:click="deletePayGroup({{ $book->id }})"
                        wire:confirm="Are you sure you want to archive this pay group and all its pay items?"
                        x-tooltip="'Archive this pay group and all pay items'"
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
                                <th class="px-4 py-2 text-left">Price</th>
                                <th class="px-4 py-2 text-left">Effective Date</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($book->payGroupDetails as $detail)
                                <tr class="border-t">
                                    <td class="px-4 py-2">{{ $detail->day_of_week }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($detail->start_time)->format('h:i A') }}
                                            -
                                            {{ \Carbon\Carbon::parse($detail->end_time)->format('h:i A') }}
                                        </td>
                                    <td class="px-4 py-2">${{ $detail->price }}</td>

                                    <td class="px-4 py-2">{{ $detail->effective_date->format('d-m-Y') }}</td>
                                    <td class="px-4 py-2">
                                        <div class="flex space-x-2">
                                            <x-filament::icon-button
                                                icon="heroicon-m-document-duplicate"
                                                color="success"
                                                size="sm"
                                                wire:click="copyPayItemRow({{ $detail->id }})"
                                                x-tooltip="'Copy Pay Item'"
                                                style="margin-right: 10px"
                                            />

                                            <x-filament::icon-button
                                                icon="heroicon-m-pencil-square"
                                                color="warning"
                                                size="sm"
                                                wire:click.stop.prevent="openEditPayItemModal({{ $detail->id }})"
                                                x-tooltip="'Edit Pay Item'"
                                                style="margin-right: 10px"
                                            />

                                            <x-filament::icon-button
                                                icon="heroicon-m-trash"
                                                color="danger"
                                                size="sm"
                                                wire:click="deletePayItemRow({{ $detail->id }})"
                                                wire:confirm="Are you sure you want to delete this pay item?"
                                                x-tooltip="'Delete Pay Item'"
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
<x-filament::modal id="new-pay-item" :close-by-clicking-away="false">

        <x-slot name="heading">
            New Pay Item
        </x-slot>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Day of Week</label>
                    <select wire:model="payItemData.day_of_week" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="">Select Day</option>
                        <option value="Weekdays - Mon Tue Wed Thu Fri">Weekdays - Mon Tue Wed Thu Fri</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                        <option value="Public Holidays">Public Holidays</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Start Time</label>
                    <input type="time" wire:model="payItemData.start_time" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">End Time</label>
                    <input type="time" wire:model="payItemData.end_time" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Price</label>
                    <input type="number" wire:model="payItemData.price" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Effective Date</label>
                    <input id="create-input" type="date" wire:model="payItemData.effective_date" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex justify-end space-x-2">
                <x-filament::button
                    color="gray"
                    wire:click="closeNewPayItemModal"
                >
                    Cancel
                </x-filament::button>
                <x-filament::button
                    color="primary"
                    wire:click="savePriceItemRow"
                >
                    Save
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament::modal>

    <!-- Edit Price Modal -->
<x-filament::modal id="edit-pay-item-modal" :close-by-clicking-away="false">
    <x-slot name="heading">Edit Pay Item</x-slot>

    <div class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Day of Week</label>
                <select wire:model="editingpayItemData.day_of_week"
                        class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Select Day</option>
                    <option value="Weekdays - Mon Tue Wed Thu Fri">Weekdays - Mon Tue Wed Thu Fri</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                    <option value="Public Holidays">Public Holidays</option>
                </select>
            </div>



            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Start Time</label>
                <input type="time" wire:model="editingpayItemData.start_time"
                       class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">End Time</label>
                <input type="time" wire:model="editingpayItemData.end_time"
                       class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Price</label>
                <input type="number" wire:model="editingpayItemData.price"
                       class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Effective Date</label>
                <input id="edit-input" type="date" wire:model="editingpayItemData.effective_date"
                       class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
        </div>
    </div>

    <x-slot name="footer">
        <div class="flex justify-end space-x-2">
            <x-filament::button color="gray" wire:click="closeEditPayItemModal">
                Cancel
            </x-filament::button>

            <x-filament::button color="primary" wire:click="updatePayItemRow">
                Update
            </x-filament::button>
        </div>
    </x-slot>
</x-filament::modal>

<x-filament::modal id="editPayGroupModal">
    <x-slot name="heading">
        Edit Pay Group
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

    </div>

    <x-slot name="footer">
        <x-filament::button wire:click="updatePayGroup" color="primary">
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
