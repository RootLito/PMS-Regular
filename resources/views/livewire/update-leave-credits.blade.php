<div class="flex-1 flex flex-col gap-10">
    <div class="w-full flex justify-between">
        <h2 class="text-5xl font-bold text-gray-700">
            LEAVE RECORD
        </h2>
        {{-- <div class="flex items-center gap-4">
            <select wire:model.live="startYear" id="startYear"
                class="shadow-sm border rounded bg-white border-gray-200 px-4 py-2 w-[200px]">
                <option value="" disabled>Select Start Year</option>
                @foreach ($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
            <span class="text-2xl">-</span>
            <select wire:model.live="endYear" id="endYear"
                class="shadow-sm border rounded bg-white border-gray-200 px-4 py-2 w-[200px]">
                <option value="" disabled>Select End Year</option>
                @foreach ($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div> --}}

    </div>

    <div class="flex-1 flex gap-10">
        <div class="flex flex-col w-[400px] self-start gap-10">
            {{-- NEW RECORD   --}}
            <div class="flex flex-col bg-white rounded-xl p-6 shadow ">
                <h2 class="font-bold text-gray-600">Create New Record</h2>
                <form wire:submit.prevent="saveRecord" class="flex flex-col mt-2">
                    @csrf


                    <p class="mt-2 text-sm font-semibold text-gray-700">Leave Type</p>
                    <select wire:model.live="leave" id="leave"
                        class="rounded-md h-10 border border-gray-200 bg-gray-50 p-2 w-full mt-2" required>
                        <option value="" disabled selected>Select Leave</option>
                        <option value="vacation_leave">Vacation Leave</option>
                        <option value="sick_leave">Sick Leave</option>
                    </select>
                    {{-- {{ $leave }} --}}


                    <p class="mt-2 text-sm font-semibold text-gray-700">Period</p>
                    <div class="w-full flex gap-2">
                        <div class="flex flex-col w-full">
                            <select wire:model.live="period_month" id="period_month"
                                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">

                                <option value="" disabled>Select Month</option>
                                @foreach ($months as $m)
                                    <option value="{{ $m['num'] }}">{{ $m['name'] }}</option>
                                @endforeach

                            </select>

                            @error('period_month')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror

                        </div>
                        <div class="flex flex-col w-full">
                            <input type="text" id="period_day" wire:model.live="period_day"
                                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">


                            @error('period_day')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <select wire:model.live="period_year" id="period_year"
                            class="rounded-md h-10 border border-gray-200 bg-gray-50 p-2 w-[96px] mt-1">
                            <option value="" disabled>Select Year</option>
                            @foreach ($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>




                    <div class="w-full flex gap-2">
                        <div class="flex flex-col flex-1">
                            <label for="selected_leave"
                                class="mt-2 text-sm  font-semibold text-gray-700">Particulars</label>
                            <select wire:model="selected_leave" id="selected_leave"
                                class="mt-1 flex-1 h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
                                <option value="">-- Code --</option>
                                @foreach ($leaveTypes as $type)
                                    <option value="{{ $type->abbreviation }}">{{ $type->abbreviation }}</option>
                                @endforeach
                            </select>

                        </div>

                        <div class="flex items-center gap-1">
                            <div class="flex flex-col w-14">
                                <label for="leaveDays" class="mt-2 text-sm  font-semibold text-gray-700">Days</label>
                                <input type="number" wire:model="leaveDays" id="leaveDays"
                                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                            </div>

                            <span class="mt-10">-</span>

                            <div class="flex flex-col w-14">
                                <label for="leaveHours" class="mt-2 text-sm  font-semibold text-gray-700">Hours</label>
                                <input type="number" wire:model="leaveHours" id="leaveHours"
                                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                            </div>

                            <span class="mt-10">-</span>

                            <div class="flex flex-col w-14">
                                <label for="leaveMinutes"
                                    class="mt-2 text-sm  font-semibold text-gray-700">Minutes</label>
                                <input type="number" wire:model="leaveMinutes" id="leaveMinutes"
                                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                            </div>
                        </div>
                    </div>

                    @if ($errors->has('leaveDays') || $errors->has('leaveHours') || $errors->has('leaveMinutes'))
                        <div class="w-full mt-2">
                            <span class="text-red-500 text-xs">
                                {{ $errors->first('selected_leave') }}
                                {{ $errors->first('leaveDays') }}
                                {{ $errors->first('leaveHours') }}
                                {{ $errors->first('leaveMinutes') }}
                            </span>
                        </div>
                    @endif


                    <p class="mt-2 text-sm font-semibold text-gray-700">Absence Undertime</p>
                    <select wire:model.live="absence_undertime" id="absence_undertime"
                        class="rounded-md h-10 border border-gray-200 bg-gray-50 p-2 w-full mt-2" required>
                        <option value="" disabled selected>Select</option>
                        <option value="wp">With Pay</option>
                        <option value="wop">Without Pay</option>
                    </select>


                    <div class="flex flex-col w-full">
                        <label for="remarks" class="mt-2 text-sm  font-semibold text-gray-700">Remarks</label>
                        <textarea wire:model="remarks" id="remarks"
                            class="mt-1 block w-full h-24 text-sm border border-gray-200 bg-gray-50 rounded-md p-2 resize-none"></textarea>
                        @error('remarks')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-slate-700 text-white py-2 text-sm rounded-md hover:bg-slate-500 cursor-pointer mt-2">
                        Create Record
                    </button>
                </form>
            </div>


            {{-- GENERATE ANNUAL --}}
            <div class="flex flex-col bg-white rounded-xl p-6 shadow ">
                <form wire:submit.prevent="generateAnnualCredits">
                    @csrf
                    <h2 class="font-bold text-gray-600">Generate Annual Credits</h2>
                    <select wire:model.live="annualCredits" id="annualCredits"
                        class="rounded-md h-10 border border-gray-200 bg-gray-50 p-2 w-full mt-2">
                        <option value="" disabled>Select Year</option>
                        @foreach ($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="w-full bg-slate-700 text-white py-2 text-sm rounded-md hover:bg-slate-500 cursor-pointer mt-2">
                        Generate
                    </button>
                </form>
            </div>



        </div>
        <div class="flex-1 bg-white p-6 rounded-xl shadow self-start">
            <div class="flex w-full justify-between">
                <div class="flex flex-col text-xs">
                    <div class="flex">
                        <span class="w-52">NAME: </span>
                        <span>{{ $fullname }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-52">OFFICE:</span>
                        <span>{{ $office }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-52">DATE OF ORIGINAL APPOINTMENT:</span>
                        <span>{{ $appointed_date }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-52">DATE OF TRANSFERRED:</span>
                        <span></span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <select wire:model.live="endYear" id="endYear"
                        class="shadow-sm border rounded bg-white border-gray-200 px-4 py-2 w-[200px]  h-10">
                        <option value="">All Year</option>
                        @foreach ($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="w-full bg-green-700 h-10 text-white py-2 text-sm rounded-md hover:bg-green-500 cursor-pointer flex items-center justify-center gap-2">
                        <i class="fa-solid fa-file-export"></i>
                        Export
                    </button>

                    <button type="submit"
                        class="w-full bg-slate-700 h-10 text-white py-2 text-sm rounded-md hover:bg-slate-500 cursor-pointer flex items-center justify-center gap-2">
                        <i class="fa-solid fa-print"></i>
                        Print
                    </button>

                </div>
            </div>

            <div class="overflow-auto mt-6">
                <table class="min-w-full table-fixed border border-gray-200 text-xs mb-2">
                    <thead>
                        <tr class="bg-gray-100 text-center">
                            <th rowspan="2" class="border border-gray-200 p-2 w-24 align-middle" width="30%">
                                PERIOD</th>
                            <th rowspan="2" class="border border-gray-200 p-2 w-32 align-middle">PARTICULARS</th>
                            <th colspan="4" class="border border-gray-200 p-2">VACATION LEAVE</th>
                            <th colspan="4" class="border border-gray-200 p-2">SICK LEAVE</th>
                            <th rowspan="2" class="border border-gray-200 p-2 w-32 align-middle">
                                Date & Action<br>
                                <span class="block text-xs">Taken on Application for Leave</span>
                            </th>
                            <th rowspan="3" class="border border-gray-200 p-2">Action</th>
                        </tr>
                        <tr class="bg-gray-100 text-center text-[10px]">
                            <th class="border border-gray-200 p-1">EARNED</th>
                            <th class="border border-gray-200 p-1">Absence<br>Undertime<br>W/ Pay</th>
                            <th class="border border-gray-200 p-1">BALANCE</th>
                            <th class="border border-gray-200 p-1">Absence<br>Undertime<br>W/o Pay</th>
                            <th class="border border-gray-200 p-1">EARNED</th>
                            <th class="border border-gray-200 p-1">Absence<br>Undertime<br>W/ Pay</th>
                            <th class="border border-gray-200 p-1">BALANCE</th>
                            <th class="border border-gray-200 p-1">Absence<br>Undertime<br>W/o Pay</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $lastYear = null;
                            $yearEndBalance = [];

                            // Get year-end balances (last occurrence per year)
                            foreach ($leaveRecords as $r) {
                                $y = $r['period_year'];
                                $yearEndBalance[$y]['vac'] = $r['balance_vacation'] ?? '';
                                $yearEndBalance[$y]['sick'] = $r['balance_sick'] ?? '';
                            }
                        @endphp

                        @forelse ($leaveRecords as $rec)
                            @if ($lastYear !== $rec['period_year'])
                                @php
                                    $vacFinal = $yearEndBalance[$rec['period_year']]['vac'] ?? '';
                                    $sickFinal = $yearEndBalance[$rec['period_year']]['sick'] ?? '';
                                @endphp

                                <tr class="bg-slate-700 text-center font-bold text-white">
                                    <td colspan="2" class="p-2 text-left">
                                        {{ $rec['period_year'] }}
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ $vacFinal !== '' ? number_format($vacFinal, 2) : '' }}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ $sickFinal !== '' ? number_format($sickFinal, 2) : '' }}</td>
                                    <td></td>
                                    <td></td>
                                    <td class="p-2">
                                        <button class="text-red-500 text-xs bg-white p-1 rounded w-full cursor-pointer"
                                            wire:click="deleteYear('{{ $rec['period_year'] }}')">
                                            Delete
                                        </button>
                                    </td>
                                </tr>

                                @php
                                    $lastYear = $rec['period_year'];
                                @endphp
                            @endif


                            <tr class="text-center">
                                <td class="border border-gray-200 p-1 text-left whitespace-nowrap">
                                    {{ $rec['period'] }}</td>
                                <td class="border border-gray-200 p-1 whitespace-nowrap">
                                    <div class="w-full flex justify-between">
                                        <span>{{ $rec['particulars'][0] ?? '' }}</span>
                                        <span>{{ $rec['particulars'][1] ?? '' }}</span>
                                    </div>
                                </td>

                                <td class="border border-gray-200 p-1 whitespace-nowrap">
                                    {{ isset($rec['earned_vacation']) ? number_format($rec['earned_vacation'], 2) : '' }}
                                </td>
                                <td class="border border-gray-200 p-1 whitespace-nowrap">
                                    {{ !empty($rec['absence_w_vacation']) ? $rec['absence_w_vacation'] : '' }}
                                </td>
                                <td class="border border-gray-200 p-1 whitespace-nowrap">
                                    {{ isset($rec['balance_vacation']) ? number_format($rec['balance_vacation'], 2) : '' }}
                                </td>
                                <td class="border border-gray-200 p-1 whitespace-nowrap">
                                    {{ !empty($rec['absence_wo_vacation']) ? $rec['absence_wo_vacation'] : '' }}
                                </td>

                                <td class="border border-gray-200 p-1 whitespace-nowrap">
                                    {{ isset($rec['earned_sick']) ? number_format($rec['earned_sick'], 2) : '' }}
                                </td>
                                <td class="border border-gray-200 p-1 whitespace-nowrap">
                                    {{ !empty($rec['absence_w_sick']) ? $rec['absence_w_sick'] : '' }}
                                </td>
                                <td class="border border-gray-200 p-1 whitespace-nowrap">
                                    {{ isset($rec['balance_sick']) ? number_format($rec['balance_sick'], 2) : '' }}
                                </td>
                                <td class="border border-gray-200 p-1 whitespace-nowrap">
                                    {{ !empty($rec['absence_wo_sick']) ? $rec['absence_wo_sick'] : '' }}
                                </td>

                                <td class="border border-gray-200 p-1 whitespace-nowrap">
                                    {{ $rec['remarks'] ?? '' }}</td>

                                <td class="border border-gray-200 p-1 whitespace-nowrap">
                                    {{-- <button class="text-blue-500 text-xs mr-2">Edit</button> --}}
                                    <button class="text-red-500 text-xs">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center p-2 text-gray-500">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>
