<div class="flex-1 flex flex-col gap-10">
    <div class="w-full flex justify-between">
        <h2 class="text-5xl font-bold text-gray-700">
            LEAVE RECORD
        </h2>

        <div class="flex items-center gap-4">
            <select wire:model.live="startYear" id="startYear"
                class="shadow-sm border rounded bg-white border-gray-200 px-4 py-2 w-[200px]">
                <option value="" disabled>Select Start Year</option>
                @foreach($years as $year)
                <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
            <span class="text-2xl">-</span>
            <select wire:model.live="endYear" id="endYear"
                class="shadow-sm border rounded bg-white border-gray-200 px-4 py-2 w-[200px]">
                <option value="" disabled>Select End Year</option>
                @foreach($years as $year)
                <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>

    </div>

    <div class="flex-1 flex gap-10">
        <div class="w-[400px] bg-white rounded-xl p-6 shadow">
            <h2 class="font-bold text-gray-600">Create New Record</h2>
            <form action="" class="flex flex-col mt-4">
                @csrf


                <div class="w-full flex gap-2">
                    <div class="flex flex-col w-100s">
                        <label for="" class="mt-4 text-sm">Period</label>
                        <input type="text" id="last_name" wire:model="last_name"
                            class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                        @error('last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="" class="mt-4 text-sm">From</label>
                        <input type="text" id="last_name" wire:model="last_name"
                            class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                        @error('last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="" class="mt-4 text-sm">To</label>
                        <input type="text" id="last_name" wire:model="last_name"
                            class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                        @error('last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>




                <div class="w-full flex gap-2">
                    <div class="flex flex-col w-100">
                        <label for="" class="mt-4 text-sm">Particulars</label>
                        <select wire:model="selected_leave"
                            class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
                            <option value="">-- Code --</option>
                            @foreach ($leaveTypes as $type)
                            <option value="{{ $type->abbreviation }}">{{ $type->leave_type }}</option>
                            @endforeach
                        </select>
                        @error('selected_leave')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror

                    </div>

                    <div class="flex items-center gap-1">
                        <div class="flex flex-col w-14">
                            <label for="" class="mt-4 text-sm">Day(s)</label>
                            <input type="text" id="last_name" wire:model="last_name"
                                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                            @error('last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <span class="mt-10">-</span>

                        <div class="flex flex-col w-14">
                            <label for="" class="mt-4 text-sm">Hour(s)</label>
                            <input type="text" id="last_name" wire:model="last_name"
                                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                            @error('last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <span class="mt-10">-</span>

                        <div class="flex flex-col w-14">
                            <label for="" class="mt-4 text-sm">Minute(s)</label>
                            <input type="text" id="last_name" wire:model="last_name"
                                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                            @error('last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <label for="" class="mt-4 text-sm">Remarks</label>
                <textarea id="" wire:model=""
                    class="mt-1 block w-full h-32 border border-gray-200 bg-gray-50 rounded-md p-2 resize-none"></textarea>
                @error('last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                <button type="submit"
                    class="w-full bg-slate-700 text-white py-2 rounded-md hover:bg-slate-500 cursor-pointer mt-4">
                    Apply
                </button>
            </form>
        </div>
        <div class="flex-1 bg-white p-6 rounded-xl shadow">
            <div class="flex w-full justify-between">
                <div class="flex flex-col">
                    <div class="flex">
                        <span>DATE OF ORIGINAL APPOINTMENT:</span>
                        <span>______________</span>
                    </div>
                    <div class="flex">
                        <span>DATE OF TRANSFERRED:</span>
                        <span>______________</span>
                    </div>
                </div>
                <div class="flex flex-col">
                    <div class="flex">
                        <span>NAME: </span>
                        <span>______________</span>
                    </div>
                    <div class="flex">
                        <span>OFFICE:</span>
                        <span>______________</span>
                    </div>
                </div>
            </div>

            <div class="overflow-auto mt-6">
                <table class="min-w-full table-fixed border border-black text-xs">
                    <thead>
                        <tr class="bg-gray-100 text-center">
                            <th rowspan="2" class="border border-black p-2 w-24 align-middle">PERIOD</th>
                            <th rowspan="2" class="border border-black p-2 w-32 align-middle">PARTICULARS</th>
                            <th colspan="4" class="border border-black p-2">VACATION LEAVE</th>
                            <th colspan="4" class="border border-black p-2">SICK LEAVE</th>
                            <th rowspan="2" class="border border-black p-2 w-32 align-middle">
                                Date & Action<br>
                                <span class="block text-xs">Taken on Application for Leave</span>
                            </th>
                            <th rowspan="3" class="border border-black p-2">Action</th>
                        </tr>
                        <tr class="bg-gray-100 text-center text-[10px]">
                            <th class="border border-black p-1">EARNED</th>
                            <th class="border border-black p-1">Absence<br>Undertime<br>W/ Pay</th>
                            <th class="border border-black p-1">BALANCE</th>
                            <th class="border border-black p-1">Absence<br>Undertime<br>W/o Pay</th>
                            <th class="border border-black p-1">EARNED</th>
                            <th class="border border-black p-1">Absence<br>Undertime<br>W/ Pay</th>
                            <th class="border border-black p-1">BALANCE</th>
                            <th class="border border-black p-1">Absence<br>Undertime<br>W/o Pay</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td class="border border-black p-1">Oct 2025</td>
                            <td class="border border-black p-1">Regular Credit</td>
                            <td class="border border-black p-1">1.25</td>
                            <td class="border border-black p-1">0.00</td>
                            <td class="border border-black p-1">10.75</td>
                            <td class="border border-black p-1">0.00</td>
                            <td class="border border-black p-1">1.25</td>
                            <td class="border border-black p-1">0.00</td>
                            <td class="border border-black p-1">12.00</td>
                            <td class="border border-black p-1">0.00</td>
                            <td class="border border-black p-1"></td>
                            <td class="border border-black p-1">
                                <select class="w-full">
                                    <option value="">Status</option>
                                    <option value="">Cancelled</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>