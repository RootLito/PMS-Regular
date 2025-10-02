<div class="h-full w-full flex flex-col gap-10 ">
    <div class="p-6 bg-white rounded-xl shadow">
        <form wire:submit.prevent="save" class="w-full flex flex-col">
            @csrf
            <h2 class="text-xl text-gray-700 font-bold mb-6">
                Add Salary
            </h2>

            <div class="w-full flex gap-2 flex-wrap">
                <div class="flex-1 min-w-[200px]">
                    <label for="monthly_salary" class="block text-sm text-gray-700">
                        Monthly Rate <span class="text-red-400">*</span>
                    </label>
                    <input type="number" id="monthly_salary" wire:model.live="monthly_salary"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                    @error('monthly_salary')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label for="gross" class="block text-sm text-gray-700">
                        Gross<span class="text-red-400">*</span></label>
                    <input type="number" id="gross" wire:model="gross"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 " readonly disabled>
                    @error('gross')
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

    <div class="flex-1 flex flex-col p-6 rounded-xl bg-white h-full overflow-auto shadow-sm">
        <h2 class="text-xl font-bold mb-4 text-gray-700">Salary Records</h2>
        <div class="overflow-auto">
            <table class="min-w-full mt-4 table-auto text-sm">
                <thead class="bg-gray-100 text-left text-gray-600">
                    <tr class="border-b border-t border-gray-200">
                        <th class="px-4 py-3 text-nowrap" width="40%">Monthly Rate</th>
                        <th class="px-4 py-2 text-nowrap" width="40%">Gross</th>
                        <th class="px-4 py-2 text-nowrap" width="20%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($salaries as $item)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        @if ($editingId === $item->id)
                        <td class="px-4 py-2">
                            <input type="number" wire:model.defer="editMonthlySalary"
                                class="block w-full py-1 border border-gray-200 bg-gray-50 rounded-md px-2" />
                            @error('editMonthlySalary')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </td>
                        <td class="px-4 py-2">
                            <input type="text" wire:model.defer="editGross"
                                class="block w-full py-1 border border-gray-200 bg-gray-50 rounded-md px-2"/>
                            @error('editGross')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </td>
                        <td class="px-4 py-2 flex gap-2 items-center">
                            <button wire:click="cancelEdit"
                                class="bg-red-500 text-white py-1 px-2 rounded hover:bg-red-600" title="Cancel">
                                <i class="fas fa-times"></i>
                            </button>
                            <button wire:click="updateSalary"
                                class="bg-green-500 text-white py-1 px-2 rounded hover:bg-green-600" title="Save">
                                <i class="fas fa-check"></i>
                            </button>
                        </td>
                        @elseif ($deletingId === $item->id)
                        <td class="px-4 py-2">{{ number_format($item->monthly_salary, 2) }}</td>
                        <td class="px-4 py-2">{{ $item->gross }}</td>
                        <td class="px-4 py-2 flex gap-2 items-center">
                            <button wire:click="cancelDelete"
                                class="bg-gray-500 text-white py-1 px-2 rounded hover:bg-gray-600"
                                title="Cancel Delete">
                                <i class="fas fa-times"></i>
                            </button>
                            <button wire:click="deleteSalaryConfirmed"
                                class="bg-red-600 text-white py-1 px-2 rounded hover:bg-red-700" title="Confirm Delete">
                                <i class="fas fa-check"></i>
                            </button>
                        </td>
                        @else
                        <td class="px-4 py-2">₱ {{ number_format($item->monthly_salary, 2) }}</td>
                        <td class="px-4 py-2">₱ {{ $item->gross }}</td>
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
                        <td colspan="3" class="px-4 py-4 text-center text-gray-500">No salary records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($salaries->hasPages())
        <div class="w-full flex justify-between items-end mt-auto">
            <div class="flex justify-center text-gray-600 mt-2 text-xs select-none">
                @php
                $from = $salaries->firstItem();
                $to = $salaries->lastItem();
                $total = $salaries->total();
                @endphp
                Showing {{ $from }} to {{ $to }} of {{ number_format($total) }} results
            </div>
            <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center mt-4 text-xs">
                <ul class="inline-flex items-center space-x-1 select-none">
                    @if ($salaries->onFirstPage())
                    <li class="text-gray-400 cursor-not-allowed px-4 py-2 rounded ">&lt;</li>
                    @else
                    <li>
                        <button wire:click="previousPage"
                            class="px-4 py-2 rounded hover:bg-gray-200 cursor-pointer bg-white shadow-sm">&lt;</button>
                    </li>
                    @endif

                    @php
                    $current = $salaries->currentPage();
                    $last = $salaries->lastPage();

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

                        @if ($salaries->hasMorePages())
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

@if (session()->has('message'))
<div class="mt-4 text-green-600">
    {{ session('message') }}
</div>
@endif
</div>