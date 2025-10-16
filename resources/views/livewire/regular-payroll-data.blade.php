<div class="flex-1 flex-col relative gap-2">
    <div class="w-full flex justify-between items-center mb-6 p-6 bg-white rounded-xl">
        <h2 class="font-black text-gray-700">PAYROLL SUMMARY</h2>
        <div class="flex text-sm gap-2">
            {{-- Office Selection Dropdown --}}
            <div class="w-150 relative rounded border border-gray-200 shadow-sm cursor-pointer">
                <div class="truncate h-10 flex items-center px-4 cursor-pointer" wire:click="toggleOffices">
                    @if (!empty($office))
                    {{ implode(', ', $office) }}
                    @else
                    All Offices
                    @endif
                </div>

                @if ($showOffices)
                <div
                    class="w-full absolute top-full left-0 mt-1 bg-white border border-gray-300 rounded shadow p-2 z-10 max-h-60 overflow-y-auto">
                    <div class="w-full flex justify-between items-center mb-2 pb-2 border-b border-gray-200">
                        <p class="text-gray-600 text-xs">Select Office(s)</p>
                        <button wire:click="proceed"
                            class="bg-blue-700 text-white font-semibold px-2 py-1 rounded hover:bg-blue-600 text-xs">
                            Proceed
                        </button>
                    </div>

                    @foreach ($officeOptions as $officeName)
                    <label class="block text-sm cursor-pointer">
                        <input type="checkbox" value="{{ $officeName }}" wire:model="office" class="mr-2">
                        {{ $officeName }}
                    </label>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Month Dropdown --}}
            <select wire:model.live="month" class="py-1 border border-gray-200 shadow-sm rounded-md px-2 bg-white">
                <option value="" disabled>Select Month</option>
                @foreach ($months as $num => $name)
                <option value="{{ $num }}">{{ $name }}</option>
                @endforeach
            </select>

            {{-- Year Dropdown --}}
            <select wire:model.live="year" class="py-1 border border-gray-200 shadow-sm rounded-md px-2 bg-white">
                <option value="" disabled>Select Year</option>
                @foreach ($years as $yearOption)
                <option value="{{ $yearOption }}">{{ $yearOption }}</option>
                @endforeach
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
            <p class="text-xs font-bold">BFAR-REGIONAL OFFICE</p>
            <p class="text-xs font-bold uppercase mb-4">PERIOD COVERED: {{ $months[$month] ?? '' }} 1-30, {{ $year ?? ''
                }}</p>
            <table class="table-auto border-collapse w-full text-xs"
                style="font-size: 10px; font-family: 'Arial Narrow';">

                <thead>
                    <tr>
                        <th class="border border-gray-300 px-2 py-1" rowspan="4">NAME OF EMPLOYEE/<br>POSITION</th>
                        <th class="border border-gray-300 px-2 py-1" rowspan="4" class="bg-blue-100">MONTHLY
                            SALARY<br>OTHER INCOME<br>AMOUNT</th>
                        <th class="border border-gray-300 px-2 py-1" rowspan="4">EARNED<br>FOR<br>PERIOD</th>
                        <th class="border border-gray-300 px-2 py-1" colspan="6">DEDUCTIONS</th>
                        <th class="border border-gray-300 px-2 py-1" rowspan="4">DEDUCTIONS</th>
                        <th class="border border-gray-300 px-2 py-1" rowspan="4">NET PAY</th>
                        <th class="border border-gray-300 px-2 py-1" rowspan="2" colspan="2">NET DUE</th>
                    </tr>
                    <tr>
                        <th class="border border-gray-300 px-2 py-1" rowspan="3">W/TAX</th>
                        <th class="border border-gray-300 px-2 py-1" rowspan="3">PHIC</th>
                        <th class="border border-gray-300 px-2 py-1" rowspan="3">GSIS LIFE &</th>
                        <th class="border border-gray-300 px-2 py-1" rowspan="3"> P'IBIG 1 & 2</th>
                        <th class="border border-gray-300 px-2 py-1" rowspan="1" colspan="2">OTHERS</th>
                    </tr>
                    <tr>
                        <th class="border border-gray-300 px-2 py-1" rowspan="2">CODE</th>
                        <th class="border border-gray-300 px-2 py-1" rowspan="2">AMOUNT</th>
                        <th class="border border-gray-300 px-2 py-1" rowspan="2">1ST</th>
                        <th class="border border-gray-300 px-2 py-1" rowspan="2">2ND</th>
                    </tr>
                </thead>

                {{-- <tbody>
                    @foreach ($employeesByOffice as $office => $employees)
                    <tr>
                        <td colspan="13" class="bg-green-200 font-bold border border-gray-300 p-1">{{ $office }}</td>
                    </tr>

                    @foreach ($employees as $employee)
                    @php
                    $c = $employee->contribution;
                    @endphp
                    <tr>
                        <td class="border border-gray-300 px-2 py-1">
                            {{ $employee->first_name }} {{ $employee->last_name }}<br>
                            <span class="italic text-gray-600">{{ $employee->position }}</span>
                        </td>

                        <td class="border border-gray-300 px-2 py-1">
                            <div class="flex flex-col">
                                <span>{{ number_format($employee->monthly_rate, 2) }}</span>
                                <span>2,000.00</span>
                            </div>

                        </td>

                        <td class="border border-gray-300 px-2 py-1">
                            {{ number_format(($employee->monthly_rate ?? 0) + 2000, 2) }}
                        </td>

                        <td class="border border-gray-300 px-2 py-1">
                            {{ ($c->tax ?? 0) != 0 ? number_format($c->tax, 2) : '-' }}
                        </td>

                        <td class="border border-gray-300 px-2 py-1">
                            {{ ($c->phic ?? 0) != 0 ? number_format($c->phic, 2) : '-' }}
                        </td>

                        <td class="border border-gray-300 px-2 py-1">
                            {{ ($c->gsis_ps ?? 0) != 0 ? number_format($c->gsis_ps, 2) : '-' }}
                        </td>


                        <td class="border border-gray-300 px-2 py-1">
                            <div class="flex flex-col">
                                <span>{{ ($c->hdmf_ps ?? null) ? number_format($c->hdmf_ps, 2) : '' }}</span>
                                <span>{{ ($c->hdmf_mp2 ?? null) ? number_format($c->hdmf_mp2, 2) : '' }}</span>
                            </div>
                        </td>



                        @php
                        $fieldsToCheck = [
                        'hdmf_mpl', 'hdmf_hl', 'gsis_pol', 'gsis_consoloan', 'gsis_emer', 'gsis_cpl', 'gsis_gfal',
                        'g_mpl', 'g_lite', 'bfar_provident', 'dareco', 'ucpb_savings', 'isda_savings_loan',
                        'isda_savings_cap_con', 'tagumcoop_sl', 'tagum_coop_cl', 'tagum_coop_sc', 'tagum_coop_rs',
                        'tagum_coop_ers_gasaka_suretech_etc', 'nd', 'lbp_sl'
                        ];

                        $fieldsWithValues = collect($fieldsToCheck)->filter(function ($field) use ($c) {
                        return !empty($c?->$field) && $c->$field != 0;
                        });
                        @endphp


                        <td class="border border-gray-300 px-2 py-1 text-center">
                            <div class="flex flex-col text-left">
                                @foreach ($fieldsWithValues as $field)
                                <span>{{ strtoupper(str_replace('_', ' ', $field)) }}</span>
                                @endforeach
                            </div>
                        </td>

                        <td class="border border-gray-300 px-2 py-1 text-right">
                            <div class="flex flex-col">
                                @foreach ($fieldsWithValues as $field)
                                <span>{{ number_format($c->$field, 2) }}</span>
                                @endforeach
                            </div>
                        </td>


                        <td class="border border-gray-300 px-2 py-1 text-right">
                            @php
                            $total =
                            ($c->tax ?? 0) +
                            ($c->phic ?? 0) +
                            ($c->gsis_ps ?? 0) +
                            ($c->hdmf_ps ?? 0) +
                            ($c->hdmf_mp2 ?? 0) +
                            ($c->hdmf_mpl ?? 0) +
                            ($c->hdmf_hl ?? 0) +
                            ($c->gsis_pol ?? 0) +
                            ($c->gsis_consoloan ?? 0) +
                            ($c->gsis_emer ?? 0) +
                            ($c->gsis_cpl ?? 0) +
                            ($c->gsis_gfal ?? 0) +
                            ($c->g_mpl ?? 0) +
                            ($c->g_lite ?? 0) +
                            ($c->bfar_provident ?? 0) +
                            ($c->dareco ?? 0) +
                            ($c->ucpb_savings ?? 0) +
                            ($c->isda_savings_loan ?? 0) +
                            ($c->isda_savings_cap_con ?? 0) +
                            ($c->tagumcoop_sl ?? 0) +
                            ($c->tagum_coop_cl ?? 0) +
                            ($c->tagum_coop_sc ?? 0) +
                            ($c->tagum_coop_rs ?? 0) +
                            ($c->tagum_coop_ers_gasaka_suretech_etc ?? 0) +
                            ($c->nd ?? 0) +
                            ($c->lbp_sl ?? 0);
                            @endphp
                            {{ ($total ?? 0) != 0 ? number_format($total, 2) : '-' }}
                        </td>


                        <td class="border border-gray-300 px-2 py-1 text-right">
                            @php
                            $monthlyRate = $employee->monthly_rate ?? 0;
                            $adjustedRate = $monthlyRate + 2000;
                            $total =
                            ($c->tax ?? 0) +
                            ($c->phic ?? 0) +
                            ($c->gsis_ps ?? 0) +
                            ($c->hdmf_ps ?? 0) +
                            ($c->hdmf_mp2 ?? 0) +
                            ($c->hdmf_mpl ?? 0) +
                            ($c->hdmf_hl ?? 0) +
                            ($c->gsis_pol ?? 0) +
                            ($c->gsis_consoloan ?? 0) +
                            ($c->gsis_emer ?? 0) +
                            ($c->gsis_cpl ?? 0) +
                            ($c->gsis_gfal ?? 0) +
                            ($c->g_mpl ?? 0) +
                            ($c->g_lite ?? 0) +
                            ($c->bfar_provident ?? 0) +
                            ($c->dareco ?? 0) +
                            ($c->ucpb_savings ?? 0) +
                            ($c->isda_savings_loan ?? 0) +
                            ($c->isda_savings_cap_con ?? 0) +
                            ($c->tagumcoop_sl ?? 0) +
                            ($c->tagum_coop_cl ?? 0) +
                            ($c->tagum_coop_sc ?? 0) +
                            ($c->tagum_coop_rs ?? 0) +
                            ($c->tagum_coop_ers_gasaka_suretech_etc ?? 0) +
                            ($c->nd ?? 0) +
                            ($c->lbp_sl ?? 0);
                            $netPay = $adjustedRate - $total;
                            @endphp
                            {{ ($netPay ?? 0) != 0 ? number_format($netPay, 2) : '-' }}
                        </td>


                        <td class="border border-gray-300 px-2 py-1 text-right">
                            {{ number_format($netPay / 2, 2) }}
                        </td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            {{ number_format($netPay / 2, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>

                    @endforeach
                    @endforeach
                </tbody> --}}
                <tbody>
                    @foreach ($employeesByOffice as $office => $employees)
                    <tr>
                        <td colspan="13" class="font-bold border bg-slate-700 text-white p-1">{{ $office }}</td>
                    </tr>
                    @foreach ($employees as $employee)
                    @php
                    $c = $employee->contribution;
                    @endphp
                    <tr>
                        <td class="border border-gray-300 px-2 py-1">
                            {{ $employee->first_name }} {{ $employee->last_name }}<br>
                            <span class="italic text-gray-600">{{ $employee->position }}</span>
                        </td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            <div class="flex flex-col">
                                <span>{{ number_format($employee->monthly_rate, 2) }}</span>
                                <span>2,000.00</span>
                            </div>
                        </td>
                        <td class="border border-gray-300 px-2 py-1">
                            {{ number_format(($employee->monthly_rate ?? 0) + 2000, 2) }}
                        </td>
                        <td class="border border-gray-300 px-2 py-1">
                            {{ ($c->tax ?? 0) != 0 ? number_format($c->tax, 2) : '-' }}
                        </td>
                        <td class="border border-gray-300 px-2 py-1">
                            {{ ($c->phic ?? 0) != 0 ? number_format($c->phic, 2) : '-' }}
                        </td>
                        <td class="border border-gray-300 px-2 py-1">
                            {{ ($c->gsis_ps ?? 0) != 0 ? number_format($c->gsis_ps, 2) : '-' }}
                        </td>
                        <td class="border border-gray-300 px-2 py-1">
                            <div class="flex flex-col">
                                <span>{{ ($c->hdmf_ps ?? null) ? number_format($c->hdmf_ps, 2) : '' }}</span>
                                <span>{{ ($c->hdmf_mp2 ?? null) ? number_format($c->hdmf_mp2, 2) : '' }}</span>
                            </div>
                        </td>
                        @php
                        $fieldsToCheck = [
                        'hdmf_mpl', 'hdmf_hl', 'gsis_pol', 'gsis_consoloan', 'gsis_emer', 'gsis_cpl', 'gsis_gfal',
                        'g_mpl', 'g_lite', 'bfar_provident', 'dareco', 'ucpb_savings', 'isda_savings_loan',
                        'isda_savings_cap_con', 'tagumcoop_sl', 'tagum_coop_cl', 'tagum_coop_sc', 'tagum_coop_rs',
                        'tagum_coop_ers_gasaka_suretech_etc', 'nd', 'lbp_sl'
                        ];
                        $fieldsWithValues = collect($fieldsToCheck)->filter(function ($field) use ($c) {
                        return !empty($c?->$field) && $c->$field != 0;
                        });
                        @endphp
                        <td class="border border-gray-300 px-2 py-1 text-center">
                            <div class="flex flex-col text-left">
                                @foreach ($fieldsWithValues as $field)
                                <span>{{ strtoupper(str_replace('_', ' ', $field)) }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            <div class="flex flex-col">
                                @foreach ($fieldsWithValues as $field)
                                <span>{{ number_format($c->$field, 2) }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            @php
                            $total = collect($fieldsToCheck)->sum(fn($field) => $c->$field ?? 0)
                            + ($c->tax ?? 0)
                            + ($c->phic ?? 0)
                            + ($c->gsis_ps ?? 0)
                            + ($c->hdmf_ps ?? 0)
                            + ($c->hdmf_mp2 ?? 0);
                            @endphp
                            {{ ($total ?? 0) != 0 ? number_format($total, 2) : '-' }}
                        </td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            @php
                            $monthlyRate = $employee->monthly_rate ?? 0;
                            $adjustedRate = $monthlyRate + 2000;
                            $netPay = $adjustedRate - $total;
                            @endphp
                            {{ ($netPay ?? 0) != 0 ? number_format($netPay, 2) : '-' }}
                        </td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            {{ number_format($netPay / 2, 2) }}
                        </td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            {{ number_format($netPay / 2, 2) }}
                        </td>
                    </tr>
                    @endforeach

                    {{-- TOTAL ROWS FOR THIS OFFICE --}}
                    {{-- @php
                    $totals = $totalsByOffice[$office] ?? [];
                    $totalSalary = $totals['monthly_rate'] ?? 0;
                    $totalPera = $totals['pera'] ?? 0;
                    $totalUacs = $totalSalary + $totalPera;
                    @endphp --}}

                    <tr>
                        <td class="border border-gray-300 px-2 py-1 text-left">TOTAL SALARY:</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            {{ number_format($totalsByOffice[$office]['monthly_rate'] ?? 0, 2) }}
                        </td>
                        <td colspan="11" class="border border-gray-300 px-2 py-1"></td>
                    </tr>

                    <tr>
                        <td class="border border-gray-300 px-2 py-1 text-left">OTHER IN CODE TOTAL (PERA):</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            {{ number_format($totalsByOffice[$office]['pera'] ?? 0, 2) }}
                        </td>
                        <td colspan="11" class="border border-gray-300 px-2 py-1"></td>
                    </tr>

                    <tr class="font-bold">
                        <td class="border border-gray-300 px-2 py-1 text-left">TOTAL UACS 14.1002:</td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            {{ number_format($totalsByOffice[$office]['uacs'] ?? 0, 2) }}
                        </td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            earn period
                        </td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            {{ number_format($totalsByOffice[$office]['tax'] ?? 0, 2) }}
                        </td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            {{ number_format($totalsByOffice[$office]['phic'] ?? 0, 2) }}
                        </td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            {{ number_format($totalsByOffice[$office]['gsis_ps'] ?? 0, 2) }}
                        </td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            {{ number_format(($totalsByOffice[$office]['hdmf_ps'] ?? 0) +
                            ($totalsByOffice[$office]['hdmf_mp2'] ?? 0), 2) }}
                        </td>

                        <td class="border border-gray-300 px-2 py-1 text-right">
                            
                        </td>

                        <td class="border border-gray-300 px-2 py-1 text-right">
                            {{ number_format($totalsByOffice[$office]['totalOthers'] ?? 0, 2) }}
                        </td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            {{ number_format($totalsByOffice[$office]['totalDeductions'] ?? 0, 2) }}
                        </td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            {{ number_format($totalsByOffice[$office]['net_pay'] ?? 0, 2) }}
                        </td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            {{ number_format($totalsByOffice[$office]['tax'] ?? 0, 2) }}
                        </td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            {{ number_format($totalsByOffice[$office]['tax'] ?? 0, 2) }}
                        </td>
                        {{-- <td colspan="6" class="border border-gray-300 px-2 py-1"></td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            {{ number_format($totalsByOffice[$office]['pera'] ?? 0, 2) }}
                        </td>
                        <td class="border border-gray-300 px-2 py-1 text-right">
                            {{ number_format($totalsByOffice[$office]['uacs'] ?? 0, 2) }}
                        </td> --}}
                        {{-- <td colspan="2" class="border border-gray-300 px-2 py-1"></td> --}}
                    </tr>

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>