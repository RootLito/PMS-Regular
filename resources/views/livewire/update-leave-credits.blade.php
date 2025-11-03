<div class="flex-1 flex flex-col gap-10">
    <div class="w-full flex justify-between">
        <h2 class="text-5xl font-bold text-gray-700">
            LEAVE RECORD
        </h2>


        <div class="flex items-center gap-4">
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
        </div>

    </div>

    <div class="flex-1 flex gap-10">
        <div class="w-[400px] bg-white rounded-xl p-6 shadow self-start">
            <h2 class="font-bold text-gray-600">Create New Record</h2>
            <form wire:submit.prevent="saveRecord" class="flex flex-col mt-4">
                @csrf

                <div class="flex flex-col w-full">
                    <label for="period" class="mt-4 text-sm">Period</label>
                    <input type="text" id="period" wire:model="period"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                    @error('period')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="w-full flex gap-2">
                    <div class="flex flex-col">
                        <label for="selected_leave" class="mt-4 text-sm">Particulars</label>
                        <select wire:model="selected_leave" id="selected_leave"
                            class="mt-1 flex-1 h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
                            <option value="">-- Code --</option>
                            @foreach ($leaveTypes as $type)
                                <option value="{{ $type->abbreviation }}">{{ $type->leave_type }}</option>
                            @endforeach
                        </select>

                    </div>

                    <div class="flex items-center gap-1">
                        <div class="flex flex-col w-14">
                            <label for="leaveDays" class="mt-4 text-sm">Days</label>
                            <input type="number" wire:model="leaveDays" id="leaveDays"
                                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                        </div>

                        <span class="mt-10">-</span>

                        <div class="flex flex-col w-14">
                            <label for="leaveHours" class="mt-4 text-sm">Hours</label>
                            <input type="number" wire:model="leaveHours" id="leaveHours"
                                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                        </div>

                        <span class="mt-10">-</span>

                        <div class="flex flex-col w-14">
                            <label for="leaveMinutes" class="mt-4 text-sm">Minutes</label>
                            <input type="number" wire:model="leaveMinutes" id="leaveMinutes"
                                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                        </div>
                    </div>


                </div>

                <div class="w-full mt-2">
                    @if ($errors->has('leaveDays') || $errors->has('leaveHours') || $errors->has('leaveMinutes'))
                        <span class="text-red-500 text-xs">
                            {{ $errors->first('selected_leave') }}
                            {{ $errors->first('leaveDays') }}
                            {{ $errors->first('leaveHours') }}
                            {{ $errors->first('leaveMinutes') }}
                        </span>
                    @endif
                </div>


                <div class="flex flex-col w-full">
                    <label for="remarks" class="mt-4 text-sm">Remarks</label>
                    <textarea wire:model="remarks" id="remarks"
                        class="mt-1 block w-full h-32 border border-gray-200 bg-gray-50 rounded-md p-2 resize-none"></textarea>
                    @error('remarks')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-slate-700 text-white py-2 rounded-md hover:bg-slate-500 cursor-pointer mt-4">
                    Apply
                </button>
            </form>

        </div>
        <div class="flex-1 bg-white p-6 rounded-xl shadow">
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
                <div class="flex flex-col">
                    {{-- <div class="flex">
                        <span>NAME: </span>
                        <span>{{ $fullname }}</span>
                    </div>
                    <div class="flex">
                        <span>OFFICE:</span>
                        <span>{{ $office }}</span>
                    </div> --}}
                </div>
            </div>

            <div class="overflow-auto mt-6">
                <table class="min-w-full table-fixed border border-gray-200 text-xs">
                    <thead>
                        <tr class="bg-gray-100 text-center">
                            <th rowspan="2" class="border border-gray-200 p-2 w-24 align-middle" width="25%">
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
                        @php $currentYear = null; @endphp

                        @foreach ($leaveRecords as $record)
                            @php
                                $year = substr($record->period, -4); 
                            @endphp

         
                            @if ($year !== $currentYear)
                                <tr class="font-bold" style="background-color: #fcd5b4">
                                    <td colspan="12" class="p-2">YEAR: {{ $year }}</td>
                                </tr>
                                @php $currentYear = $year; @endphp
                            @endif

                            <tr class="text-center">
                                <td class="border border-gray-200 p-1 text-left" width="20%">{{ $record->period }}
                                </td>
                                <td class="border border-gray-200 p-1">
                                    <div class="flex-1 flex justify-between">
                                        <span>{{ $record->particulars_type }}</span>
                                        <span>{{ $record->particulars }}</span>
                                    </div>
                                </td>
                                <td class="border border-gray-200 p-1">{{ $record->earned_vacation }}</td>
                                <td class="border border-gray-200 p-1">{{ $record->absence_w_vacation }}</td>
                                <td class="border border-gray-200 p-1">{{ number_format($record->balance_vacation, 2) }}</td>
                                <td class="border border-gray-200 p-1">{{ $record->absence_wo_vacation }}</td>
                                <td class="border border-gray-200 p-1">{{ $record->earned_sick }}</td>
                                <td class="border border-gray-200 p-1">{{ $record->absence_w_sick }}</td>
                                <td class="border border-gray-200 p-1">{{ number_format($record->balance_sick, 2) }}</td>
                                <td class="border border-gray-200 p-1">{{ $record->absence_wo_sick }}</td>
                                <td class="border border-gray-200 p-1">{{ $record->remarks }}</td>
                                <td class="border border-gray-200 p-1">
                                    <select class="w-full">
                                        <option value="">Status</option>
                                        <option value="">Cancelled</option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>
