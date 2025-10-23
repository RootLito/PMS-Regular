<div class="flex-1 flex flex-col bg-white rounded-xl p-6 shadow">
    <div class="w-full flex justify-between mb-4 gap-2 mt-4">
        <input type="text" placeholder="Search by name..."
            class="border border-gray-300 bg-gray-50 rounded px-4 py-2 w-[500px]" wire:model.live="search">

        <div class="flex gap-2">
            <select class="shadow-sm border rounded border-gray-200 px-4 py-2" wire:model.live="office">
                <option value="">All Offices</option>
                @foreach ($offices as $office)
                <option value="{{ $office }}">{{ $office }}</option>
                @endforeach
            </select>
            <select wire:model.live="sortOrder" class="shadow-sm border rounded border-gray-200 px-4 py-2 w-[200px]">
                <option value="">Sort By</option>
                <option value="asc">A-Z</option>
                <option value="desc">Z-A</option>
            </select>
            <button wire:click.prevent="exportPayrollAnalysis"
                class="bg-slate-700 text-white font-semibold px-4 py-1 rounded cursor-pointer hover:bg-slate-600">
                <i class="fa-regular fa-file-excel mr-1"></i>Export to Excel</button>
        </div>
    </div>


    <div class="overflow-auto mb-2">
        <div class="max-w-full overflow-auto mt-6 mb-2">
            {{-- <table class="min-w-max table-auto text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr class="border-b border-t border-gray-200">
                        <th class="px-4 py-3 whitespace-nowrap sticky left-0 bg-gray-100 z-20">SL Code</th>
                        <th class="px-4 py-3 whitespace-nowrap sticky left-[82px] bg-gray-100 z-20">SL Name</th>
                        <th class="px-4 py-3 whitespace-nowrap">September 1-15</th>
                        <th class="px-4 py-3 whitespace-nowrap">September 16-30</th>
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
                    @foreach ($employees as $employee)
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-2 whitespace-nowrap sticky left-0 bg-white">{{ $employee->id }}</td>
                        <td class="px-4 py-2 whitespace-nowrap sticky left-[82px] bg-white">{{ $employee->full_name }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <!-- September 1-15 -->
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <!-- September 16-30 -->
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <!-- Total Net Amount -->
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->tax ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->phic ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->gsis_ps ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->hdmf_ps ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->hdmf_mp2 ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->hdmf_mpl ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->hdmf_hl ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->gsis_pol ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->gsis_consoloan ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->gsis_emer ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->gsis_cpl ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->gsis_gfal ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->g_mpl ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->g_lite ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->bfar_provident ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->dareco ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->ucpb_savings ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->isda_savings_loan ?? '' }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->isda_savings_cap_con ?? ''
                            }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->tagumcoop_sl ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->tagum_coop_cl ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->tagum_coop_sc ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->tagum_coop_rs ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{
                            $employee->contribution->tagum_coop_ers_gasaka_suretech_etc ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->nd ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->lbp_sl ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->total_charges ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->total_salary ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->pera ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->gross ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->rate_per_month ?? '' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <!-- GSIS- GS (12%) -->
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $employee->contribution->leave_wo ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table> --}}


            <table table class="table-auto border-collapse w-full text-xs"
                style="font-size: 10px; font-family: 'Arial Narrow';">
                <thead class="bg-gray-100 text-left">
                    <tr class="border-b border-t border-gray-200">
                        <th class="px-4 py-3 whitespace-nowrap sticky left-0 bg-gray-100 z-20">SL Code</th>
                        <th class="px-4 py-3 whitespace-nowrap sticky left-[0px] bg-gray-100 z-20">SL Name</th>

                        <th class="px-4 py-3 whitespace-nowrap">First Half</th>
                        <th class="px-4 py-3 whitespace-nowrap">Second Half</th>
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
                    @foreach ($employees as $employee)
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>