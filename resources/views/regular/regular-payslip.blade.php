@extends('layouts.app')

@section('title', 'Payslip')

@section('content')
    <div class="flex-1 grid place-items-center p-10 rel">

        <div class="print">
            <div class="header">
                <div class="date">
                    <div>
                        <h1 class="text-center">P A Y S L I P</h1>
                        {{ $monthName }} {{ $selectedYear }}
                    </div>

                </div>
                <div class="image">
                    <img src="{{ asset('images/top.png') }}" alt="header">
                </div>
            </div>

            <table class="w-full table-auto border-collapse">
                <tr class="">
                    <td class="border-l border-black px-1">EMPLOYEE NAME:</td>
                    <td colspan="3" class="border-r border-black">
                        {{ $employeeData->last_name }}, {{ $employeeData->first_name }}
                        @if (!empty($employeeData->suffix))
                            {{ $employeeData->suffix }}
                        @endif
                        {{ strtoupper(substr($employeeData->middle_initial, 0, 1)) }}.
                    </td>

                    <td colspan="4" class="border-r border-black px-1">DIVISION</td>
                </tr>
                <tr>
                    <td class="border-l border-b border-black px-1">POSITION</td>
                    <td colspan="3" class="border-r border-b border-black">{{ $employeeData->position ?? '' }}</td>
                    <td colspan="4" class="border-r border-b border-black px-1">SECTION</td>
                </tr>

                <tr>
                    <td colspan="2" class="border-x border-black"></td>
                    <td colspan="2" class="border-x border-black"></td>
                    <td colspan="2" class="text-center border-r border-black" width="120px">D-A-T-E</td>
                    <td colspan="2" class="border-r border-black"></td>
                </tr>

                <tr>
                    <td colspan="2" class="border-l border-r border-b border-black px-1">GROSS EARNINGS</td>
                    <td colspan="2" class="border-r border-b border-black px-1">DEDUCTIONS</td>
                    <td class="border-b border-black px-1 text-center">START</td>
                    <td class="border-r border-b border-black px-1 text-center">END</td>
                    <td colspan="2" class="border-b border-r border-black px-1">NET EARNINGS</td>
                </tr>
                {{-- BORDERS   --}}
                <tr>
                    <td class="border-l border-black px-1">Basic Salary</td>
                    <td class="text-right px-1">
                        {{ $employeeData->monthly_rate ? number_format($employeeData->monthly_rate, 2) : '' }}
                    </td>
                    <td class="border-l border-black px-1">TAX</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->tax ? number_format($employeeData->contribution->tax, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black px-1">1ST PERIOD</td>
                    <td class="border-r border-black text-right px-1">
                        {{ $employeeData->contribution?->first_half ? number_format($employeeData->contribution->first_half, 2) : '' }}
                    </td>
                </tr>

                <tr>
                    <td class="border-l border-black px-1">P E R A</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->pera ? number_format($employeeData->contribution->pera, 2) : '' }}
                    </td>
                    <td class="border-l border-black px-1">PHIC</td>
                    <td class="border-r border-black text-right px-1">
                        {{ $employeeData->contribution?->phic ? number_format($employeeData->contribution->phic, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black"></td>
                    <td class="border-r border-black"></td>
                </tr>

                <tr>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black px-1">GSIS LIFE & RET.</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->gsis_ps ? number_format($employeeData->contribution->gsis_ps, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black px-1">2ND PERIOD</td>
                    <td class="border-r border-black text-right px-1">
                        {{ $employeeData->contribution?->second_half ? number_format($employeeData->contribution->second_half, 2) : '' }}
                    </td>
                </tr>

                <tr>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black px-1">P'IBIG 1</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->hdmf_mp2 ? number_format($employeeData->contribution->hdmf_mp2, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black"></td>
                    <td class="border-r border-black"></td>
                </tr>

                <tr>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black px-1">P'IBIG 2</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->hdmf_ps ? number_format($employeeData->contribution->hdmf_ps, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black"></td>
                    <td class="border-r border-black"></td>
                </tr>

                <tr>
                    <td class="border-l border-black px-1">SG: {{ $employeeData->salary_grade ?? '' }}</td>
                    <td></td>
                    <td class="border-l border-black px-1">G POL-REG</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->gsis_pol ? number_format($employeeData->contribution->gsis_pol, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black"></td>
                    <td class="border-r border-black"></td>
                </tr>

                <tr>
                    <td class="border-l border-black px-1">Step: {{ $employeeData->step ?? '' }}</td>
                    <td></td>
                    <td class="border-l border-black px-1">G EDUC LOAN</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->gsis_cpl ? number_format($employeeData->contribution->gsis_cpl, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black"></td>
                    <td class="border-r border-black"></td>
                </tr>

                <tr>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black px-1">G CONSOL</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->gsis_consoloan ? number_format($employeeData->contribution->gsis_consoloan, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black"></td>
                    <td class="border-r border-black"></td>
                </tr>

                <tr>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black px-1">G HELP</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->gsis_emer ? number_format($employeeData->contribution->gsis_emer, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black"></td>
                    <td class="border-r border-black"></td>
                </tr>

                <tr>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black px-1">G ELA</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->gsis_gfal ? number_format($employeeData->contribution->gsis_gfal, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black"></td>
                    <td class="border-r border-black"></td>
                </tr>

                <tr>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black px-1">HDMF MPL</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->hdmf_mpl ? number_format($employeeData->contribution->hdmf_mpl, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black"></td>
                    <td class="border-r border-black"></td>
                </tr>

                <tr>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black px-1">HDMF HL</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->hdmf_hl ? number_format($employeeData->contribution->hdmf_hl, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black"></td>
                    <td class="border-r border-black"></td>
                </tr>
                <tr>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black px-1">DARECO</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->dareco ? number_format($employeeData->contribution->dareco, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black"></td>
                    <td class="border-r border-black"></td>
                </tr>

                <tr>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black px-1">BFAR DISALLOWANCE</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->bfar_provident ? number_format($employeeData->contribution->bfar_provident, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black"></td>
                    <td class="border-r border-black"></td>
                </tr>

                <tr>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black px-1">PROVIDENT FUND</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->bfar_provident ? number_format($employeeData->contribution->bfar_provident, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black"></td>
                    <td class="border-r border-black"></td>
                </tr>

                <tr>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black px-1">SSS Cont.</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->gsis_ps ? number_format($employeeData->contribution->gsis_ps, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black"></td>
                    <td class="border-r border-black"></td>
                </tr>

                <tr>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black px-1">UCPB</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->ucpb_savings ? number_format($employeeData->contribution->ucpb_savings, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black"></td>
                    <td class="border-r border-black"></td>
                </tr>

                <tr>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black px-1">ISLAI Cap. Cont.</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->isda_savings_cap_con ? number_format($employeeData->contribution->isda_savings_cap_con, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black"></td>
                    <td class="border-r border-black"></td>
                </tr>

                <tr>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black px-1">TAGUM Coop SC</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->tagum_coop_sc ? number_format($employeeData->contribution->tagum_coop_sc, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black"></td>
                    <td class="border-r border-black"></td>
                </tr>

                <tr>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black px-1">TAGUM Coop SAV</td>
                    <td class="text-right px-1">
                        {{ $employeeData->contribution?->tagumcoop_sl ? number_format($employeeData->contribution->tagumcoop_sl, 2) : '' }}
                    </td>
                    <td class="border-l border-black"></td>
                    <td></td>
                    <td class="border-l border-black"></td>
                    <td class="border-r border-black"></td>
                </tr>






                {{-- END  --}}
                <tr class="border border-black font-bold">
                    <td class="px-1">GROSS PAY</td>
                    <td class="border-r border-black text-right px-1">
                        {{ number_format(($employeeData->monthly_rate ?? 0) + ($employeeData->contribution->pera ?? 0), 2) }}
                    </td>
                    <td class="px-1">TOTAL DEDUCTIONS</td>
                    <td class="border-r border-black text-right px-1">
                        {{ number_format($employeeData->contribution->total_charges ?? 0, 2) }}
                    </td>
                    <td></td>
                    <td class="border-r border-black"></td>
                    <td class="px-1">NET PAY</td>
                    <td class="text-right px-1">
                        {{ number_format($employeeData->contribution->total_net_amount ?? 0, 2) }}
                    </td>
                </tr>

            </table>

            <div class="w-full grid grid-cols-3">
                <div>
                    <p class="mb-8">Prepared by:</p>
                    <p class="underline">{{ $assigned->prepared2->name ?? '-' }}</p>
                    <p>{{ $assigned->prepared2->designation ?? '-' }}</p>
                </div>
                <div>
                    <p class="mb-8">Certified by:</p>
                    <p class="underline">{{ $assigned->checked->name ?? '-' }}</p>
                    <p>{{ $assigned->checked->designation ?? '-' }}</p>
                </div>
                <div>
                    <p class="mb-8">Acknowledge:</p>
                    <p class="underline">{{ $employeeData->last_name }}, {{ $employeeData->first_name }}
                        @if (!empty($employeeData->suffix))
                            {{ $employeeData->suffix }}
                        @endif
                        {{ strtoupper(substr($employeeData->middle_initial, 0, 1)) }}.
                    </p>
                    <p>{{ $employeeData->position ?? '' }}</p>
                </div>
            </div>
        </div>


        <div class="w-[320px] bg-white absolute top-26 right-10 flex flex-col p-6 rounded-xl date">
            <h2 class="font-bold text-gray-700">Release Date</h2>
            <form method="GET" action="{{ route('regular-employee.payslip', ['employeeId' => $employeeData->id]) }}">
                <label for="month" class="text-xs mt-4 text-gray-600">Month</label>
                <select name="month" id="month"
                    class="text-sm w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                    @foreach ($months as $num => $name)
                        <option value="{{ $num }}" {{ $selectedMonthInt == $num ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>



                <label for="year" class="text-xs mt-4 text-gray-600">Year</label>
                <select name="year" id="year"
                    class="text-sm w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                    @foreach ($years as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                            {{ $year }}</option>
                    @endforeach
                </select>

                <button type="submit"
                    class="text-sm bg-green-700 hover:bg-green-800 h-10 mt-4 rounded-lg cursor-pointer text-white w-full">
                    <i class="fas fa-check mr-2"></i>Apply
                </button>
            </form>
            <button type="button" onclick="window.print()"
                class="text-sm bg-slate-700 hover:bg-slate-800 h-10 mt-2 rounded-lg cursor-pointer text-white w-full"><i
                    class="fas fa-print mr-2"></i>Print</button>

            <a href="/regular-employee"
                class="text-sm bg-red-400 hover:bg-red-500 text-white h-10 mt-2 rounded-lg flex items-center justify-center w-full">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>
@endsection
