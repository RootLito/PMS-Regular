<div class="h-full w-full flex flex-col gap-10 ">
    <div class="p-6 bg-white rounded-xl shadow">
        <form wire:submit.prevent="save" class="w-full flex flex-col">
            @csrf
            <h2 class="text-xl text-gray-700 font-bold mb-6">
                Add Salary
            </h2>

            <div class="w-full grid grid-cols-10 gap-2">
                <div class="flex-1 min-w-[120px]">
                    <label for="salary_grade" class="block text-sm text-gray-700">Salary Grade <span
                            class="text-red-400">*</span></label>
                    <input type="number" id="salary_grade" wire:model.defer="salary_grade"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2" min="1"
                        step="1" />
                    @error('salary_grade')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                </div>

                @for ($i = 1; $i <= 8; $i++) <div class="flex-1 min-w-[120px]">
                    <label for="step_{{ $i }}" class="block text-sm text-gray-700">Step {{ $i }} <span
                            class="text-red-400">*</span></label>
                    <input type="number" id="step_{{ $i }}" wire:model.defer="step_{{ $i }}"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2" min="0"
                        />
                    @error('step_' . $i)<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
            </div>
            @endfor

            <div class="w-full mt-auto">
                <button type="submit"
                    class="w-full bg-slate-700 text-white h-10 rounded-md hover:bg-slate-500 cursor-pointer">
                    Save
                </button>
                @if ($errors->any())
                <span class="text-red-500 text-xs">Error encountered</span>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="flex-1 flex flex-col p-6 rounded-xl bg-white h-full overflow-auto shadow-sm">
    <h2 class="text-xl font-bold mb-4 text-gray-700">Salary Records</h2>
    <div class="overflow-auto">
        <table class="min-w-full mt-4 table-auto text-sm">
            <thead class="bg-gray-100 text-left text-gray-600">
                <tr class="border-b border-t border-gray-200">
                    <th class="px-4 py-3" width="5%">SG</th>
                    @for ($i = 1; $i <= 8; $i++) 
                        <th class="px-4 py-3" width="10%">Step {{ $i }}</th>
                    @endfor
                        <th class="px-4 py-3" width="15%">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($salaries as $item)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    @if ($editingId === $item->id)
                    <td class="px-4 py-2">
                        <input type="number" wire:model.defer="editSalaryGrade"
                            class="block w-full py-1 border border-gray-200 bg-gray-50 rounded-md px-2" min="1"
                            step="1" />
                        @error('editSalaryGrade') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </td>
                    @for ($i = 1; $i <= 8; $i++) <td class="px-4 py-2">
                        <input type="number" wire:model.defer="editStep_{{ $i }}"

                            class="block w-full py-1 border border-gray-200 bg-gray-50 rounded-md px-2" min="0"/>
                        @error("editStep_{$i}") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </td>
                        @endfor
                        <td class="px-4 py-2 flex gap-2 items-center">
                            <button wire:click="cancelEdit"
                                class="bg-red-500 text-white py-1 px-2 rounded hover:bg-red-600" title="Cancel"><i
                                    class="fas fa-times"></i></button>
                            <button wire:click="updateSalary"
                                class="bg-green-500 text-white py-1 px-2 rounded hover:bg-green-600" title="Save"><i
                                    class="fas fa-check"></i></button>
                        </td>


                        @elseif ($deletingId === $item->id)
                        <td class="px-4 py-2">{{ $item->salary_grade }}</td>
                        @for ($i = 1; $i <= 8; $i++) <td class="px-4 py-2">{{ number_format($item->{'step_'.$i}, 3) }}
                            </td>
                            @endfor
                            <td class="px-4 py-2 flex gap-2 items-center">
                                <button wire:click="cancelDelete"
                                    class="bg-gray-500 text-white py-1 px-2 rounded hover:bg-gray-600"
                                    title="Cancel Delete"><i class="fas fa-times"></i></button>
                                <button wire:click="deleteSalaryConfirmed"
                                    class="bg-red-600 text-white py-1 px-2 rounded hover:bg-red-700"
                                    title="Confirm Delete"><i class="fas fa-check"></i></button>
                            </td>
                            @else
                            <td class="px-4 py-2 font-bold text-gray-600">{{ $item->salary_grade }}</td>
                            @for ($i = 1; $i <= 8; $i++) <td class="px-4 py-2">₱ {{ number_format($item->{'step_'.$i}) }}</td>
                                @endfor
                                <td class="px-4 py-2 flex gap-2 items-center">
                                    <button wire:click="edit({{ $item->id }})"
                                        class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-400"><i
                                            class="fas fa-edit"></i> Edit</button>
                                    <button wire:click="confirmDelete({{ $item->id }})"
                                        class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-400"><i
                                            class="fas fa-trash-alt"></i> Delete</button>
                                </td>
                    @endif
                </tr>

                @empty
                <tr>
                    <td colspan="10" class="px-4 py-4 text-center text-gray-500">No salary records found.</td>
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