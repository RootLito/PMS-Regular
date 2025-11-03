<div class="flex-1 flex gap-10 flex-col">
    <div class="w-full flex justify-between">
        <h2 class="text-5xl font-bold text-gray-700">
            LEAVE CREDITS
        </h2>
    </div>
    <div class="flex-1 flex gap-10">
        <div class="flex-1 flex flex-col gap-10">
            <div class="flex-1 h-[900px] bg-white rounded-xl p-6 shadow">
                <h2 class="text-gray-700 font-bold mb-4">Table 1</h2>
                <p class="text-sm text-gray-700">Table showing conversion for <strong>leave with pay</strong> and
                    <strong>leave with without pay</strong>.
                </p>
                <table class="min-w-full table-auto text-sm mt-4">
                    <thead class="bg-gray-100 text-left">
                        <tr class="border-b border-t border-gray-200">
                            <th class="px-4 py-2 text-nowrap">Day</th>
                            <th class="px-4 py-2 text-nowrap">Leave Earned</th>
                            <th class="px-4 py-2 text-nowrap">Month</th>
                            <th class="px-4 py-2 text-nowrap">Leave Earned</th>
                            {{-- <th class="px-4 py-2 text-nowrap">Vication Leave (WOP)</th> --}}
                            {{-- <th class="px-4 py-2 text-nowrap">Leave Earned</th>a --}}
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < 30; $i++)
                            <tr class="text-xs hover:bg-gray-100 odd:bg-white even:bg-gray-50">
                                <td class="px-4">{{ $days[$i]['day'] }}</td>
                                <td class="px-4">{{ $days[$i]['leave'] }}</td>
                                @if (isset($months[$i]))
                                    <td class="px-4">{{ $months[$i]['month'] }}</td>
                                    <td class="px-4">{{ $months[$i]['leave'] }}</td>
                                @else
                                    <td class="px-4"></td>
                                    <td class="px-4"></td>
                                @endif
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            <div class="flex-1 h-[900px] bg-white rounded-xl p-6 shadow">
                <h2 class="text-gray-700 font-bold mb-4">Table 2</h2>
                <p class="text-sm text-gray-700">Table showing conversion of working hours/minutes into fractions of a
                    day
                </p>
                <p class="text-xs italic text-gray-700 font-bold">Based on 8-hour day</p>

                <div class="w-full flex gap-6">
                    <table class="self-start table-auto text-sm mt-4 w-[300px]">
                        <thead class="bg-gray-100 text-left">
                            <tr class="border-b border-t border-gray-200">
                                <th class="px-4 py-2 text-nowrap">Hours</th>
                                <th class="px-4 py-2 text-nowrap">Equivalent Day</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hours as $hour)
                                <tr class="text-xs hover:bg-gray-100 odd:bg-white even:bg-gray-50">
                                    <td class="px-4 py-1">{{ $hour['hour'] }}</td>
                                    <td class="px-4 py-1">{{ $hour['equiv'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <table class="flex-1 table-auto text-sm mt-4">
                        <thead class="bg-gray-100 text-left">
                            <tr class="border-b border-t border-gray-200">
                                <th class="px-4 py-2 text-nowrap">Minutes</th>
                                <th class="px-4 py-2 text-nowrap">Equiv. Day</th>
                                <th class="px-4 py-2 text-nowrap">Minutes</th>
                                <th class="px-4 py-2 text-nowrap">Equiv. Day</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < count($minutesLeft); $i++)
                                <tr class="text-xs hover:bg-gray-100 odd:bg-white even:bg-gray-50">
                                    <td class="px-4">{{ $minutesLeft[$i]['minute'] }}</td>
                                    <td class="px-4">.{{ substr(explode('.', $minutesLeft[$i]['equiv'])[1], 0, 3) }}
                                    </td>
                                    <td class="px-4">{{ $minutesRight[$i]['minute'] }}</td>
                                    <td class="px-4">.{{ substr(explode('.', $minutesRight[$i]['equiv'])[1], 0, 3) }}
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>


            <div class="flex-1 h-[900px] bg-white rounded-xl p-6 shadow">
                <h2 class="text-gray-700 font-bold mb-4">Leave Types List</h2>

                <table class="min-w-full table-auto text-sm mt-4">
                    <thead class="bg-gray-100 text-left">
                        <tr class="border-b border-t border-gray-200">
                            <th class="px-4 py-2 text-nowrap" width="30%">Abbreviation</th>
                            <th class="px-4 py-2 text-nowrap">Fullname</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveTypes as $leaveType)
                            <tr>
                                <td class="px-4 py-2">{{ $leaveType->abbreviation }}</td>
                                <td class="px-4 py-2">{{ $leaveType->leave_type }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-2 text-center text-gray-500">
                                    No data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="w-[400px] flex flex-col gap-10">
            <form class="w-[400px] bg-white rounded-xl p-6 shadow" wire:submit.prevent="leavePay">
                <h2 class="text-gray-700 font-bold mb-4">Table 1</h2>
                <div>
                    <label class="block text-sm text-gray-700">Year credit (12-month equivalent)</label>
                    <input type="text" wire:model="leave_with_pay"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
                    @error('leave_with_pay')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                {{-- <div>
                    <label class="block text-sm text-gray-700">Leave with Pay <span class="font-bold text-sm">Day/Month
                            (1-30)</span></label>
                    <input type="text" wire:model="leave_with_pay"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
                    @error('leave_with_pay')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div> --}}
                {{-- <div class="mt-2">
                    <label class="block text-sm text-gray-700">Leave without Pay <span
                            class="font-bold text-sm">Day/Month
                            (1-30)</span></label>
                    <input type="text" wire:model="leave_without_pay"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
                    @error('leave_without_pay')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div> --}}
                <button type="submit"
                    class="w-full bg-slate-700 text-white py-2 rounded-md hover:bg-slate-500 cursor-pointer mt-2 mb-2">
                    Confirm
                </button>
            </form>



            <form class="w-[400px] bg-white rounded-xl p-6 shadow" wire:submit.prevent="hourDayBase">
                <h2 class="text-gray-700 font-bold mb-4">Table 2</h2>
                <div>
                    <label class="block text-sm text-gray-700">Fullday credit (8-hour equivalent)</label>
                    <input type="text" wire:model="hour_day_base"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
                    @error('hour_day_base')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit"
                    class="w-full bg-slate-700 text-white py-2 rounded-md hover:bg-slate-500 cursor-pointer mt-2 mb-2">
                    Confirm
                </button>
            </form>


            <form class="w-[400px] bg-white rounded-xl p-6 shadow" wire:submit.prevent="leaveTypes">
                <h2 class="text-gray-700 font-bold mb-4">Leave Types</h2>

                <div>
                    <label class="block text-sm text-gray-700">Abbreviation</label>
                    <input type="text" wire:model="abbreviation"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
                    @error('abbreviation')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mt-2">
                    <label class="block text-sm text-gray-700">Leave Type</label>
                    <input type="text" wire:model="leave_type"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
                    @error('leave_type')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-slate-700 text-white py-2 rounded-md hover:bg-slate-500 cursor-pointer mt-2 mb-2">
                    Confirm
                </button>
            </form>
        </div>
    </div>
</div>
