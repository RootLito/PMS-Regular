<div class="w-full h-full flex-1 flex flex-col p-10 gap-10 relative">
    <div class="w-full flex justify-between">
        <h2 class="text-5xl font-bold text-gray-700">
            CONTRIBUTION
        </h2>
        
    </div>


    <div class="w-full h-full flex gap-10">
        <div class="w-1/2 h-full flex flex-col bg-white rounded-xl p-6 overflow-auto shadow-sm">
            <div class="flex justify-between mb-4 gap-2 mt-2">
                <input type="text" placeholder="Search by name..."
                    class="border border-gray-300 bg-gray-50 rounded px-4 py-2 w-1/2" wire:model.live="search">
                <div class="w-1/2 flex gap-2 justify-end">
                    <select class="shadow-sm border rounded border-gray-200 px-4 py-2 w-48"
                        wire:model.live="office">
                        <option value="">All Designations</option>
                        @foreach ($offices as $office)
                        <option value="{{ $office }}">{{ $office }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="sortOrder" class="shadow-sm border rounded border-gray-200 px-4 py-2 w-32">
                        <option value="">Sort By</option>
                        <option value="asc">A-Z</option>
                        <option value="desc">Z-A</option>
                    </select>
                </div>
            </div>
            <div class="overflow-auto mt-6">
                <table class="min-w-full table-auto text-sm">
                    <thead class="bg-gray-100 text-left">
                        <tr class="border-b border-t border-gray-200 text-gray-700 ">
                            <th class="px-4 py-2 ">Last Name</th>
                            <th class="px-4 py-2">First Name</th>
                            <th class="px-4 py-2">M.I.</th>
                            <th class="px-4 py-2">Office</th>
                            <th class="px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tbody>
                        @forelse ($employees as $employee)
                        <tr
                            class="border-b border-gray-200 cursor-pointer {{ $selectedEmployee === $employee->id ? 'bg-gray-300' : '' }}">
                            <td class="px-4 py-2">{{ $employee->last_name }}</td>
                            <td class="px-4 py-2">
                                {{ $employee->first_name }}{{ $employee->suffix ? ' ' . $employee->suffix . '.' : '' }}
                            </td>
                            <td class="px-4 py-2">
                                @if (!empty($employee->middle_initial))
                                {{ strtoupper(substr($employee->middle_initial, 0, 1)) }}.
                                @endif
                            </td>
                            <td class="px-4 py-2">{{ $employee->office }}</td>
                            <td class="px-4 py-2">
                                <button wire:click="employeeSelected({{ $employee->id }})"
                                    class="bg-green-700 hover:bg-green-800 text-white px-3 py-1 rounded flex items-center gap-1 cursor-pointer">
                                    {{ $selectedEmployee === $employee->id ? 'Selected' : 'Select' }}
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                                No employees found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                    </tbody>
                </table>
            </div>
            @if ($employees->hasPages())
            <div class="w-full flex justify-between items-end">
                <div class="flex justify-center text-gray-600 mt-2 text-xs select-none">
                    @php
                    $from = $employees->firstItem();
                    $to = $employees->lastItem();
                    $total = $employees->total();
                    @endphp
                    Showing {{ $from }} to {{ $to }} of {{ number_format($total) }} results
                </div>
                <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center mt-4 text-xs">
                    <ul class="inline-flex items-center space-x-1 select-none">
                        @if ($employees->onFirstPage())
                        <li class="text-gray-400 cursor-not-allowed px-4 py-2 rounded ">&lt;</li>
                        @else
                        <li>
                            <button wire:click="previousPage"
                                class="px-4 py-2 rounded hover:bg-gray-200 cursor-pointer bg-white shadow-sm">&lt;</button>
                        </li>
                        @endif
                        @php
                        $current = $employees->currentPage();
                        $last = $employees->lastPage();

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
                            @if ($employees->hasMorePages())
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
        <div class="w-1/2 h-full flex flex-col bg-white rounded-xl p-6 overflow-auto shadow-sm">
            <h2 class="text-xl font-bold text-gray-700 mb-6">Contributions</h2>

            <form class="flex-1 flex flex-col" wire:submit.prevent="save">
                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">Tax</label>
                        <input type="text" wire:model="tax"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">PHIC</label>
                        <input type="text" wire:model="phic"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">GSIS PS</label>
                        <input type="text" wire:model="gsis_ps"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">HDMF PS</label>
                        <input type="text" wire:model="hdmf_ps"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">HDMF MP2</label>
                        <input type="text" wire:model="hdmf_mp2"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">HDMF MPL</label>
                        <input type="text" wire:model="hdmf_mpl"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">HDMF HL</label>
                        <input type="text" wire:model="hdmf_hl"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">GSIS POL</label>
                        <input type="text" wire:model="gsis_pol"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">GSIS Consoloan</label>
                        <input type="text" wire:model="gsis_consoloan"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">GSIS Emergency</label>
                        <input type="text" wire:model="gsis_emer"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">GSIS CPL</label>
                        <input type="text" wire:model="gsis_cpl"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">GSIS GFAL</label>
                        <input type="text" wire:model="gsis_gfal"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">G MPL</label>
                        <input type="text" wire:model="g_mpl"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">G Lite</label>
                        <input type="text" wire:model="g_lite"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">BFAR Provident</label>
                        <input type="text" wire:model="bfar_provident"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">Dareco</label>
                        <input type="text" wire:model="dareco"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">UCPB Savings</label>
                        <input type="text" wire:model="ucpb_savings"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">ISDA Savings Loan</label>
                        <input type="text" wire:model="isda_savings_loan"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">ISDA Savings Cap Con</label>
                        <input type="text" wire:model="isda_savings_cap_con"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">Tagum Coop SL</label>
                        <input type="text" wire:model="tagumcoop_sl"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">Tagum Coop CL</label>
                        <input type="text" wire:model="tagum_coop_cl"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">Tagum Coop SC</label>
                        <input type="text" wire:model="tagum_coop_sc"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">Tagum Coop RS</label>
                        <input type="text" wire:model="tagum_coop_rs"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">Tagum Coop ERS / GASAKA / Suretech</label>
                        <input type="text" wire:model="tagum_coop_ers_gasaka_suretech_etc"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">ND</label>
                        <input type="text" wire:model="nd"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">LBP SL</label>
                        <input type="text" wire:model="lbp_sl"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">Total Charges</label>
                        <input type="text" wire:model="total_charges"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">Total Salary</label>
                        <input type="text" wire:model="total_salary"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">PERA</label>
                        <input type="text" wire:model="pera"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">Gross</label>
                        <input type="text" wire:model="gross"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">Rate per Month</label>
                        <input type="text" wire:model="rate_per_month"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-gray-700 text-sm">Leave WO</label>
                        <input type="text" wire:model="leave_wo"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                            is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>


                <button type="submit" class="mt-4 mb-4 w-full h-10 bg-slate-700 rounded-md text-white cursor-pointer"
                    @if(is_null($selectedEmployee)) disabled @endif>
                    CONFIRM
                </button>
            </form>
        </div>
    </div>
</div>