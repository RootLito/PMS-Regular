<div class="flex-1 flex flex-col bg-white rounded-xl p-6 shadow">

    <div class="w-full flex justify-between gap-2">
        <div class="flex gap-2 w-full mb-12">
            <input type="text" placeholder="Search by name..."
                class="border border-gray-300 bg-gray-50 rounded px-4 py-2 w-[500px]"
                wire:model.live="search">


            <select class="shadow-sm border rounded border-gray-200 px-4 py-2 ml-auto" wire:model.live="office">
                <option value="">All Offices</option>
                @foreach ($offices as $office)
                <option value="{{ $office }}">{{ $office }}</option>
                @endforeach
            </select>
            {{-- <select wire:model.live="sortOrder"
                class="shadow-sm border rounded border-gray-200 px-4 py-2 w-[200px]">
                <option value="">Sort By</option>
                <option value="asc">A-Z</option>
                <option value="desc">Z-A</option>
            </select> --}}
            <select wire:model.live="month" class="py-1 border border-gray-200 shadow-sm rounded-md px-2 bg-white">
                <option value="" disabled>Select Month</option>
                @foreach ($months as $num => $name)
                <option value="{{ $num }}">{{ $name }}</option>
                @endforeach
            </select>
            <button wire:click.prevent="exportPayrollAnalysis"
                class="bg-slate-700 text-white font-semibold px-4 py-1 rounded cursor-pointer hover:bg-slate-600">
                <i class="fa-regular fa-file-excel mr-1"></i>Export to Excel</button>
        </div>
    </div>




    <div class="overflow-auto">
        <div class="max-w-full overflow-auto">
            <table table class="table-auto border-collapse w-full text-xs"
                style="font-size: 10px; font-family: 'Arial Narrow';">
                <thead class="bg-gray-100 text-left">
                    <tr class="border-b border-t border-gray-200">
                        <th class="px-4 py-3 whitespace-nowrap bg-gray-100 z-20">SL Code</th>
                        <th class="px-4 py-3 whitespace-nowrap bg-gray-100 z-20">SL Name</th>
                        <th class="px-4 py-3 whitespace-nowrap">{{ $first_half }}</th>
                        <th class="px-4 py-3 whitespace-nowrap">{{ $second_half }}</th>
                        <th class="px-4 py-3 whitespace-nowrap">Total Net Amount</th>
                        <th class="px-4 py-3 whitespace-nowrap">W/ Tax</th>
                        <th class="px-4 py-3 whitespace-nowrap">PHIC</th>
                        <th class="px-4 py-3 whitespace-nowrap">GSIS PS (9%)</th>
                        <th class="px-4 py-3 whitespace-nowrap">HDMF PS (1)</th>
                        <th class="px-4 py-3 whitespace-nowrap">HDMF-MP2 (2)</th>
                        <th class="px-4 py-3 whitespace-nowrap">HDMF-MPL (3)</th>
                        <th class="px-4 py-3 whitespace-nowrap">HDMF/ HL (1)</th>
                        <th class="px-4 py-3 whitespace-nowrap">GSIS- POL (PLREG)</th>
                        <th class="px-4 py-3 whitespace-nowrap">GSIS- CONSOLOAN</th>
                        <th class="px-4 py-3 whitespace-nowrap">GSIS- EMER</th>
                        <th class="px-4 py-3 whitespace-nowrap">GSIS- CPL</th>
                        <th class="px-4 py-3 whitespace-nowrap">GSIS-GFAL</th>
                        <th class="px-4 py-3 whitespace-nowrap">G-MPL</th>
                        <th class="px-4 py-3 whitespace-nowrap">G-LITE</th>
                        <th class="px-4 py-3 whitespace-nowrap">BFAR Provident Fund</th>
                        <th class="px-4 py-3 whitespace-nowrap">DARECO</th>
                        <th class="px-4 py-3 whitespace-nowrap">UCPB Savings</th>
                        <th class="px-4 py-3 whitespace-nowrap">ISDA SAVINGS LOAN</th>
                        <th class="px-4 py-3 whitespace-nowrap">ISDA SAVINGS CAP CON.</th>
                        <th class="px-4 py-3 whitespace-nowrap">TAGUM COOP- SL</th>
                        <th class="px-4 py-3 whitespace-nowrap">TAGUM COOP- CL ADD ON</th>
                        <th class="px-4 py-3 whitespace-nowrap">TAGUM COOP- SC</th>
                        <th class="px-4 py-3 whitespace-nowrap">TAGUM COOP- RS</th>
                        <th class="px-4 py-3 whitespace-nowrap">TAGUM COOP- ERS, GASAKA, SURETECH, ETC</th>
                        <th class="px-4 py-3 whitespace-nowrap">ND</th>
                        <th class="px-4 py-3 whitespace-nowrap">LBP SL</th>
                        <th class="px-4 py-3 whitespace-nowrap">Total Charges</th>
                        <th class="px-4 py-3 whitespace-nowrap">TOTAL SALARY</th>
                        <th class="px-4 py-3 whitespace-nowrap">PERA</th>
                        <th class="px-4 py-3 whitespace-nowrap">GROSS</th>
                        <th class="px-4 py-3 whitespace-nowrap">Rate Per Month</th>
                        <th class="px-4 py-3 whitespace-nowrap">GSIS- GS (12%)</th>
                        <th class="px-4 py-3 whitespace-nowrap">Leave w/o</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employeesByOffice as $officeName => $employees)
                    <!-- Office Name -->
                    <tr class="bg-gray-100" style="background: #b4c6e7">
                        <td colspan="38" class="px-4 py-1 font-semibold">{{ $officeName }}</td>
                    </tr>

                    <!-- Employee List per Office -->
                    @forelse ($employees as $employee)
                    @php
                    $c = $employee->contribution;
                    @endphp
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $employee->sl_code }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            {{ strtoupper($employee->last_name) }}, {{ $employee->first_name }}{{
                            $employee->middle_initial ? ' ' . strtoupper(substr($employee->middle_initial, 0, 1)) . '.'
                            : '' }}{{ $employee->suffix ? ' ' . $employee->suffix : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->first_half !== null ? number_format($c->first_half, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->second_half !== null ? number_format($c->second_half, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->total_net_amount !== null ? number_format($c->total_net_amount, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->tax !== null ? number_format($c->tax, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->phic !== null ? number_format($c->phic, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->gsis_ps !== null ? number_format($c->gsis_ps, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->hdmf_ps !== null ? number_format($c->hdmf_ps, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->hdmf_mp2 !== null ? number_format($c->hdmf_mp2, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->hdmf_mpl !== null ? number_format($c->hdmf_mpl, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->hdmf_hl !== null ? number_format($c->hdmf_hl, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->gsis_pol !== null ? number_format($c->gsis_pol, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->gsis_consoloan !== null ? number_format($c->gsis_consoloan, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->gsis_emer !== null ? number_format($c->gsis_emer, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->gsis_cpl !== null ? number_format($c->gsis_cpl, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->gsis_gfal !== null ? number_format($c->gsis_gfal, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->g_mpl !== null ? number_format($c->g_mpl, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->g_lite !== null ? number_format($c->g_lite, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->bfar_provident !== null ? number_format($c->bfar_provident, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->dareco !== null ? number_format($c->dareco, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->ucpb_savings !== null ? number_format($c->ucpb_savings, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->isda_savings_loan !== null ? number_format($c->isda_savings_loan, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->isda_savings_cap_con !== null ? number_format($c->isda_savings_cap_con, 2) : ''
                            }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->tagumcoop_sl !== null ? number_format($c->tagumcoop_sl, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->tagum_coop_cl !== null ? number_format($c->tagum_coop_cl, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->tagum_coop_sc !== null ? number_format($c->tagum_coop_sc, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->tagum_coop_rs !== null ? number_format($c->tagum_coop_rs, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->tagum_coop_ers_gasaka_suretech_etc !== null ?
                            number_format($c->tagum_coop_ers_gasaka_suretech_etc, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->nd !== null ? number_format($c->nd, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->lbp_sl !== null ? number_format($c->lbp_sl, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right font-semibold">
                            {{ $c && $c->total_charges !== null ? number_format($c->total_charges, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right font-semibold">
                            {{ $c && $c->total_salary !== null ? number_format($c->total_salary, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->pera !== null ? number_format($c->pera, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->gross !== null ? number_format($c->gross, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->rate_per_month !== null ? number_format($c->rate_per_month, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->gsis_gs !== null ? number_format($c->gsis_gs, 2) : '' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            {{ $c && $c->leave_wo !== null ? number_format($c->leave_wo, 2) : '' }}
                        </td>
                    </tr>
                    @empty
                    <!-- Fallback message when no employees in the office -->
                    <tr>
                        <td colspan="38" class="px-4 py-1 text-center text-gray-500">No records available</td>
                    </tr>
                    @endforelse

                    <!-- Office Total -->
                    <tr class="font-semibold" style="background: #c6e1b4;">
                        <td colspan="2" class="px-4 py-1 text-right">Total</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['first_half'] ?
                            number_format($officeTotals[$officeName]['first_half'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['second_half'] ?
                            number_format($officeTotals[$officeName]['second_half'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['total_net_amount'] ?
                            number_format($officeTotals[$officeName]['total_net_amount'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['tax'] ?
                            number_format($officeTotals[$officeName]['tax'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['phic'] ?
                            number_format($officeTotals[$officeName]['phic'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['gsis_ps'] ?
                            number_format($officeTotals[$officeName]['gsis_ps'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['hdmf_ps'] ?
                            number_format($officeTotals[$officeName]['hdmf_ps'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['hdmf_mp2'] ?
                            number_format($officeTotals[$officeName]['hdmf_mp2'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['hdmf_mpl'] ?
                            number_format($officeTotals[$officeName]['hdmf_mpl'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['hdmf_hl'] ?
                            number_format($officeTotals[$officeName]['hdmf_hl'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['gsis_pol'] ?
                            number_format($officeTotals[$officeName]['gsis_pol'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['gsis_consoloan'] ?
                            number_format($officeTotals[$officeName]['gsis_consoloan'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['gsis_emer'] ?
                            number_format($officeTotals[$officeName]['gsis_emer'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['gsis_cpl'] ?
                            number_format($officeTotals[$officeName]['gsis_cpl'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['gsis_gfal'] ?
                            number_format($officeTotals[$officeName]['gsis_gfal'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['g_mpl'] ?
                            number_format($officeTotals[$officeName]['g_mpl'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['g_lite'] ?
                            number_format($officeTotals[$officeName]['g_lite'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['bfar_provident'] ?
                            number_format($officeTotals[$officeName]['bfar_provident'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['dareco'] ?
                            number_format($officeTotals[$officeName]['dareco'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['ucpb_savings'] ?
                            number_format($officeTotals[$officeName]['ucpb_savings'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['isda_savings_loan'] ?
                            number_format($officeTotals[$officeName]['isda_savings_loan'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['isda_savings_cap_con'] ?
                            number_format($officeTotals[$officeName]['isda_savings_cap_con'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['tagumcoop_sl'] ?
                            number_format($officeTotals[$officeName]['tagumcoop_sl'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['tagum_coop_cl'] ?
                            number_format($officeTotals[$officeName]['tagum_coop_cl'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['tagum_coop_sc'] ?
                            number_format($officeTotals[$officeName]['tagum_coop_sc'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['tagum_coop_rs'] ?
                            number_format($officeTotals[$officeName]['tagum_coop_rs'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{
                            $officeTotals[$officeName]['tagum_coop_ers_gasaka_suretech_etc'] ?
                            number_format($officeTotals[$officeName]['tagum_coop_ers_gasaka_suretech_etc'], 2) : '-' }}
                        </td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['nd'] ?
                            number_format($officeTotals[$officeName]['nd'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['lbp_sl'] ?
                            number_format($officeTotals[$officeName]['lbp_sl'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right font-semibold">{{ $officeTotals[$officeName]['total_charges'] ?
                            number_format($officeTotals[$officeName]['total_charges'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right font-semibold">{{ $officeTotals[$officeName]['total_salary'] ?
                            number_format($officeTotals[$officeName]['total_salary'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['pera'] ?
                            number_format($officeTotals[$officeName]['pera'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['gross'] ?
                            number_format($officeTotals[$officeName]['gross'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['rate_per_month'] ?
                            number_format($officeTotals[$officeName]['rate_per_month'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['gsis_gs'] ?
                            number_format($officeTotals[$officeName]['gsis_gs'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $officeTotals[$officeName]['leave_wo'] ?
                            number_format($officeTotals[$officeName]['leave_wo'], 2) : '-' }}</td>

                    </tr>
                    
                    @endforeach

                    <tr class="py-1">
                        <td class="invisible">space</td>
                    </tr>

                    <!-- Overall Total -->
                    <tr style="background-color: #f5b084; font-weight: bold;">
                        <td colspan="2" class="px-4 py-1 text-right">Overall Total</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['first_half'] ?
                            number_format($overallTotal['first_half'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['second_half'] ?
                            number_format($overallTotal['second_half'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['total_net_amount'] ?
                            number_format($overallTotal['total_net_amount'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['tax'] ? number_format($overallTotal['tax'],
                            2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['phic'] ? number_format($overallTotal['phic'],
                            2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['gsis_ps'] ?
                            number_format($overallTotal['gsis_ps'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['hdmf_ps'] ?
                            number_format($overallTotal['hdmf_ps'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['hdmf_mp2'] ?
                            number_format($overallTotal['hdmf_mp2'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['hdmf_mpl'] ?
                            number_format($overallTotal['hdmf_mpl'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['hdmf_hl'] ?
                            number_format($overallTotal['hdmf_hl'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['gsis_pol'] ?
                            number_format($overallTotal['gsis_pol'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['gsis_consoloan'] ?
                            number_format($overallTotal['gsis_consoloan'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['gsis_emer'] ?
                            number_format($overallTotal['gsis_emer'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['gsis_cpl'] ?
                            number_format($overallTotal['gsis_cpl'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['gsis_gfal'] ?
                            number_format($overallTotal['gsis_gfal'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['g_mpl'] ?
                            number_format($overallTotal['g_mpl'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['g_lite'] ?
                            number_format($overallTotal['g_lite'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['bfar_provident'] ?
                            number_format($overallTotal['bfar_provident'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['dareco'] ?
                            number_format($overallTotal['dareco'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['ucpb_savings'] ?
                            number_format($overallTotal['ucpb_savings'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['isda_savings_loan'] ?
                            number_format($overallTotal['isda_savings_loan'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['isda_savings_cap_con'] ?
                            number_format($overallTotal['isda_savings_cap_con'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['tagumcoop_sl'] ?
                            number_format($overallTotal['tagumcoop_sl'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['tagum_coop_cl'] ?
                            number_format($overallTotal['tagum_coop_cl'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['tagum_coop_sc'] ?
                            number_format($overallTotal['tagum_coop_sc'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['tagum_coop_rs'] ?
                            number_format($overallTotal['tagum_coop_rs'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['tagum_coop_ers_gasaka_suretech_etc'] ?
                            number_format($overallTotal['tagum_coop_ers_gasaka_suretech_etc'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['nd'] ? number_format($overallTotal['nd'], 2)
                            : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['lbp_sl'] ?
                            number_format($overallTotal['lbp_sl'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right font-semibold">{{ $overallTotal['total_charges'] ?
                            number_format($overallTotal['total_charges'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right font-semibold">{{ $overallTotal['total_salary'] ?
                            number_format($overallTotal['total_salary'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['pera'] ? number_format($overallTotal['pera'],
                            2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['gross'] ?
                            number_format($overallTotal['gross'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['rate_per_month'] ?
                            number_format($overallTotal['rate_per_month'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['gsis_gs'] ?
                            number_format($overallTotal['gsis_gs'], 2) : '-' }}</td>
                        <td class="px-4 py-1 text-right">{{ $overallTotal['leave_wo'] ?
                            number_format($overallTotal['leave_wo'], 2) : '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>