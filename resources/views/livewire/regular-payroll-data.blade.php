<div class="flex-1 flex-col relative gap-2">
    <div class="w-full flex justify-between items-center mb-6 p-6 bg-white rounded-xl">
        <h2 class="font-black text-gray-700">PAYROLL SUMMARY</h2>
        <div class="flex text-sm gap-2">
            <div class="w-150  relative rounded border border-gray-200 shadow-sm cursor-pointer">
                <div class="truncate h-10 flex items-center px-4 cursor-pointer" wire:click="toggleDesignations">
                    {{-- @if (count($designation))
                    {{ implode(', ', $designation) }}
                    @else
                    All Designations
                    @endif --}}
                </div>
                {{-- @if ($showDesignations)
                <div
                    class="w-full absolute top-full left-0 mt-1 bg-white border border-gray-300 rounded shadow p-2 z-10 max-h-60 overflow-y-auto">
                    <div class="w-full flex justify-between items-center mb-2 pb-2 border-b border-gray-200">
                        <p class="text-gray-600 text-xs">Select Designation(s)</p>
                        <button wire:click="proceed"
                            class="bg-blue-700 text-white font-semibold px-2 py-1 rounded cursor-pointer hover:bg-blue-600 text-xs">Proceed</button>
                    </div>
                    @foreach ($designations as $desig)
                    <label class="block text-sm cursor-pointer">
                        <input type="checkbox" value="{{ $desig }}" wire:model="designation"
                            class=" cursor-pointer mr-2">
                        {{ $desig }}
                    </label>
                    @endforeach
                </div>
                @endif --}}
            </div>
            <select wire:model.live="cutoff" class="px-2 py-1 rounded border border-gray-200 shadow-sm cursor-pointer">
                <option value="" disabled>Select Cutoff</option>
                <option value="1-15">1st Cutoff (1-15)</option>
                <option value="16-31">2nd Cutoff (16-31)</option>
            </select>
            <select wire:model.live="month" class="py-1 border border-gray-200 shadow-sm rounded-md px-2 bg-white ">
                <option value="" disabled>Select Month</option>
                {{-- @foreach ($months as $num => $name)
                <option value="{{ $num }}" {{ $month==$num ? 'selected' : '' }}>{{ $name }}
                </option>
                @endforeach --}}
            </select>
            <select wire:model.live="year" class="py-1 border border-gray-200 shadow-sm rounded-md px-2 bg-white">
                <option value="" disabled>Select Year</option>
                {{-- @foreach ($years as $yearOption)
                <option value="{{ $yearOption }}" {{ $year==$yearOption ? 'selected' : '' }}>{{ $yearOption }}
                </option>
                @endforeach --}}
            </select>


            <button wire:click.prevent=""
                class="bg-green-700 text-white font-semibold px-4 py-1 rounded cursor-pointer hover:bg-green-600"><i
                    class="fa-solid fa-floppy-disk mr-1"></i> Save to Archive</button>
            <button wire:click.prevent=""
                class="bg-slate-700 text-white font-semibold px-4 py-1 rounded cursor-pointer hover:bg-slate-600">
                <i class="fa-regular fa-file-excel mr-1"></i>Export to Excel</button>



            {{-- <button wire:click.prevent="saveArchive"
                class="bg-green-700 text-white font-semibold px-4 py-1 rounded cursor-pointer hover:bg-green-600"><i
                    class="fa-solid fa-floppy-disk mr-1"></i> Save to Archive</button>
            <button wire:click.prevent="exportPayroll"
                class="bg-slate-700 text-white font-semibold px-4 py-1 rounded cursor-pointer hover:bg-slate-600">
                <i class="fa-regular fa-file-excel mr-1"></i>Export to Excel</button> --}}


        </div>
    </div>
    <div class="bg-white p-6 min-h-100 rounded-xl">

        <div id="payrollContent">
            <div class="w-full flex justify-center items-center gap-6 mb-2">
                <div class="flex gap-2">
                    <div class="w-20 h-20">
                        <img src="{{ asset('images/bagong_pilipinas.png') }}" alt="Bagong Pilipinas"
                            class="object-contain w-full h-full">
                    </div>
                    <div class="w-20 h-20">
                        <img src="{{ asset('images/bfar.png') }}" alt="BFAR" class="object-contain w-full h-full">
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-xs">Republic of the Philippines</p>
                    <p class="text-xs">Department of Agriculture </p>
                    <h2 class="font-bold">BUREAU OF FISHERIES AND AQUATIC RESOURCES</h2>
                    <p class="text-xs">Region XI, R. Magsaysay Ave., Davao City</p>
                </div>
                <div class="w-20 h-20">
                    <img src="{{ asset('images/gad.png') }}" alt="GAD" class="object-contain w-full h-full">
                </div>
            </div>
            <p class="text-xs font-bold">CONTRACT OF SERVICES / JOB ORDER</p>
            {{-- <h2 class="font-bold">{{ $dateRange }}</h2> --}}
            <h2 class="font-bold">Date Rage</h2>
            <table class="table-auto border-collapse w-full text-xs"
                style="font-size: 10px; font-family: 'Arial Narrow';">

                <thead>
                    <tr>
                        <th class="border border-gray-400 px-1 py-1" rowspan="4">NAME OF EMPLOYEE/<br>POSITION</th>
                        <th class="border border-gray-400 px-1 py-1" rowspan="4" class="bg-blue-100">MONTHLY
                            SALARY<br>OTHER INCOME<br>AMOUNT</th>
                        <th class="border border-gray-400 px-1 py-1" rowspan="4">EARNED<br>FOR<br>PERIOD</th>
                        <th class="border border-gray-400 px-1 py-1" colspan="6">DEDUCTIONS</th>
                        <th class="border border-gray-400 px-1 py-1" rowspan="4">DEDUCTIONS</th>
                        <th class="border border-gray-400 px-1 py-1" rowspan="4">NET PAY</th>
                        <th class="border border-gray-400 px-1 py-1" rowspan="2" colspan="2">NET DUE</th>
                    </tr>
                    <tr>
                        <th class="border border-gray-400 px-1 py-1" rowspan="3">W/TAX</th>
                        <th class="border border-gray-400 px-1 py-1" rowspan="3">PHIC</th>
                        <th class="border border-gray-400 px-1 py-1" rowspan="3">GSIS LIFE &</th>
                        <th class="border border-gray-400 px-1 py-1" rowspan="3"> P'IBIG 1 & 2</th>
                        <th class="border border-gray-400 px-1 py-1" rowspan="1" colspan="2">OTHERS</th>
                    </tr>
                    <tr>
                        <th class="border border-gray-400 px-1 py-1" rowspan="2">CODE</th>
                        <th class="border border-gray-400 px-1 py-1" rowspan="2">AMOUNT</th>
                        <th class="border border-gray-400 px-1 py-1" rowspan="2">1ST</th>
                        <th class="border border-gray-400 px-1 py-1" rowspan="2">2ND</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($employeesByOffice as $office => $employees)
                    <tr>
                        <td colspan="13" class="bg-gray-100 font-bold">{{ $office }}</td>
                    </tr>

                    @foreach ($employees as $employee)
                    @php
                    $c = $employee->contribution;
                    @endphp
                    <tr>
                        <td class="border px-1 py-1">
                            {{ $employee->first_name }} {{ $employee->last_name }}<br>
                            <span class="italic text-gray-600">{{ $employee->position }}</span>
                        </td>

                        <td class="border px-1 py-1 text-right">
                            ₱{{ number_format($employee->monthly_rate, 2) }}
                        </td>

                        <td class="border px-1 py-1 text-right">
                            ₱{{ number_format($c->total_salary ?? 0, 2) }}
                        </td>

                        <td class="border px-1 py-1 text-right">
                            ₱{{ number_format($c->tax ?? 0, 2) }}
                        </td>

                        <td class="border px-1 py-1 text-right">
                            ₱{{ number_format($c->phic ?? 0, 2) }}
                        </td>

                        <td class="border px-1 py-1 text-right">
                            ₱{{ number_format($c->gsis_ps ?? 0, 2) }}
                        </td>

                        <td class="border px-1 py-1 text-right">
                            ₱{{ number_format(($c->hdmf_ps ?? 0) + ($c->hdmf_mp2 ?? 0), 2) }}
                        </td>

                        <td class="border px-1 py-1 text-center">
                            @php
                            $knownKeys = ['tax', 'phic', 'gsis_ps', 'hdmf_ps', 'hdmf_mp2', 'total_salary',
                            'total_charges', 'gross', 'rate_per_month', 'pera', 'leave_wo'];
                            $others = collect($c?->toArray() ?? [])->filter(function($v, $k) use ($knownKeys) {
                            return !in_array($k, $knownKeys) && $v != 0 && $v !== null;
                            });
                            $firstOther = $others->keys()->first();
                            @endphp
                            {{ strtoupper(str_replace('_', ' ', $firstOther ?? '')) }}
                        </td>

                        <td class="border px-1 py-1 text-right">
                            ₱{{ number_format($others->first() ?? 0, 2) }}
                        </td>

                        <td class="border px-1 py-1 text-right">
                            ₱{{ number_format($c->total_charges ?? 0, 2) }}
                        </td>

                        <td class="border px-1 py-1 text-right">
                            @php
                            $netPay = ($c->total_salary ?? 0) - ($c->total_charges ?? 0);
                            @endphp
                            ₱{{ number_format($netPay, 2) }}
                        </td>

                        <td class="border px-1 py-1 text-right">
                            ₱{{ number_format($netPay / 2, 2) }}
                        </td>
                        <td class="border px-1 py-1 text-right">
                            ₱{{ number_format($netPay / 2, 2) }}
                        </td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>