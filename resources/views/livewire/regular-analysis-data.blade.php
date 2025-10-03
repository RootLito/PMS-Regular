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
        </div>
    </div>


    <div class="overflow-auto mb-2">
        <div class="max-w-full overflow-auto mt-6 mb-2">
            <table class="min-w-max table-auto text-sm">
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
                        <td class="px-4 py-2 whitespace-nowrap sticky left-[82px] bg-white">{{ $employee->full_name }}</td>
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
            </table>
        </div>
    </div>
</div>