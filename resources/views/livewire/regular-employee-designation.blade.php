<div class="flex-1 flex flex-col gap-10 ">
    <div class="p-6 bg-white rounded-xl shadow">
        <form wire:submit.prevent="save" class="w-full flex flex-col">
            @csrf
            <h2 class="text-xl text-gray-700 font-bold mb-6">
                Add Designation
            </h2>

            <div class="w-full flex gap-2 flex-wrap">
                <div class="w-[200px]">
                    <label for="order_no" class="block text-sm text-gray-700">
                        Order No. <span class="text-red-400">*</span>
                    </label>
                    <input type="number" id="order_no" wire:model.defer="order_no"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                    @error('order_no')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label for="office" class="block text-sm text-gray-700">Office<span
                            class="text-red-400">*</span></label>
                    <input type="text" id="office" wire:model.defer="office"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                    @error('office')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mt-auto w-[400px]">
                    <button type="submit"
                        class="w-full bg-slate-700 text-white h-10 rounded-md hover:bg-slate-500 cursor-pointer">
                        Save
                    </button>
                    @if ($errors->any())
                    <span class="text-red-500 text-xs">Error occurred</span>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="flex-1 flex flex-col p-6 bg-white rounded-xl shadow">
        <div class="w-full flex justify-between">
            <h2 class="text-xl text-gray-700 font-bold mb-6">
                Designation List
            </h2>
            <input type="text" id="search" placeholder="Search by order no. or office name" wire:model.live="search"
                class="w-1/2 h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
        </div>


        <table class="min-w-full table-auto text-sm mt-2">
            <thead class="bg-gray-100 text-left">
                <tr class="border-b border-t border-gray-200">
                    <th class="px-4 py-3 text-nowrap" width="15%">Order No.</th>
                    <th class="px-4 py-2 text-nowrap" width="65%">Office name</th>
                    <th class="px-4 py-2 text-nowrap" width="20%">Actions</th>
                </tr>
            </thead>
            <thead class="bg-gray-100 text-left">
            <tbody>
                @forelse ($offices as $item)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    @if ($editingId === $item->id)
                    <td class="px-4 py-2">
                        <input type="text" wire:model.defer="editOrderNo"
                            class="block w-full py-1 border border-gray-200 bg-gray-50 rounded-md px-2" />
                        @error('editOrderNo')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </td>
                    <td class="px-4 py-2">
                        <input type="text" wire:model.defer="editOffice"
                            class="block w-full py-1 border border-gray-200 bg-gray-50 rounded-md px-2" />
                        @error('editOffice')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </td>
                    <td class="px-4 py-2 flex gap-2 items-center">
                        <button wire:click="cancelEdit" class="bg-red-500 text-white py-1 px-2 rounded hover:bg-red-600"
                            title="Cancel">
                            <i class="fas fa-times"></i>
                        </button>
                        <button wire:click="update" class="bg-green-500 text-white py-1 px-2 rounded hover:bg-green-600"
                            title="Save">
                            <i class="fas fa-check"></i>
                        </button>
                    </td>
                    @elseif ($deletingId === $item->id)
                    <td class="px-4 py-2">{{ $item->order_no }}</td>
                    <td class="px-4 py-2">{{ $item->office }}</td>
                    <td class="px-4 py-2 flex gap-2 items-center">
                        <button wire:click="cancelDelete"
                            class="bg-gray-500 text-white py-1 px-2 rounded hover:bg-gray-600" title="Cancel Delete">
                            <i class="fas fa-times"></i>
                        </button>
                        <button wire:click="delete" class="bg-red-600 text-white py-1 px-2 rounded hover:bg-red-700"
                            title="Confirm Delete">
                            <i class="fas fa-check"></i>
                        </button>
                    </td>
                    @else
                    <td class="px-4 py-2">{{ $item->order_no }}</td>
                    <td class="px-4 py-2">{{ $item->office }}</td>
                    <td class="px-4 py-2 flex gap-2 items-center">
                        <button wire:click="edit({{ $item->id }})"
                            class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-400">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button wire:click="confirmDelete({{ $item->id }})"
                            class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-400">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-4 text-center text-gray-500">No records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if ($offices->hasPages())
        <div class="w-full flex justify-between items-end mt-auto">
            <div class="flex justify-center text-gray-600 mt-2 text-xs select-none">
                @php
                $from = $offices->firstItem();
                $to = $offices->lastItem();
                $total = $offices->total();
                @endphp
                Showing {{ $from }} to {{ $to }} of {{ number_format($total) }} results
            </div>
            <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center mt-4 text-xs">
                <ul class="inline-flex items-center space-x-1 select-none">
                    @if ($offices->onFirstPage())
                    <li class="text-gray-400 cursor-not-allowed px-4 py-2 rounded ">&lt;</li>
                    @else
                    <li>
                        <button wire:click="previousPage"
                            class="px-4 py-2 rounded hover:bg-gray-200 cursor-pointer bg-white shadow-sm">&lt;</button>
                    </li>
                    @endif

                    @php
                    $current = $offices->currentPage();
                    $last = $offices->lastPage();

                    if ($current == 1) {
                    $start = 1;
                    $end = min(3, $last);
                    } elseif ($current == $last) {
                    $start = max($last - 2, 1);
                    $end = $last;
                    } else {
                    $start = max($current - 1, 1);
                    $end = min($current + 1, $last);
                    }
                    @endphp
                    @for ($page = $start; $page <= $end; $page++) @if ($page==$current) <li
                        class="bg-slate-700 text-white px-4 py-2 rounded cursor-default">
                        {{ $page }}
                        </li>
                        @else
                        <li>
                            <button wire:click="gotoPage({{ $page }})"
                                class="px-4 py-2 rounded hover:bg-gray-200 cursor-pointer">{{ $page }}</button>
                        </li>
                        @endif
                        @endfor

                        @if ($offices->hasMorePages())
                        <li>
                            <button wire:click="nextPage"
                                class="px-4 py-2 rounded hover:bg-gray-200 cursor-pointer bg-white shadow-sm">&gt;</button>
                        </li>
                        @else
                        <li class="text-gray-400 cursor-not-allowed px-4 py-2 rounded ">&gt;</li>
                        @endif
                </ul>
            </nav>
        </div>
        @endif
    </div>
</div>