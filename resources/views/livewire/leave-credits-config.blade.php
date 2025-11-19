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
                <p class="text-sm text-gray-700">Table showing conversion for <strong>leave with pay</strong>.
                </p>

                <table class="w-full text-xs mt-4">
                    <thead class="bg-gray-100 text-left">
                        <tr class="border-b border-t border-gray-200">
                            <th class="px-4 py-2 text-nowrap">Month</th>
                            <th class="px-4 py-2 text-nowrap">Leave Earned</th>
                            <th class="px-4 py-2 text-nowrap">Day</th>
                            <th class="px-4 py-2 text-nowrap">Leave Earned</th>
                        </tr>
                    </thead>

                    <tbody>
                        @for ($i = 0; $i < 30; $i++)
                            <tr class="text-xs hover:bg-gray-100 odd:bg-white even:bg-gray-50">

                                @if ($i < count($this->yearBase))
                                    <td class="px-4">{{ $this->yearBase[$i]['year'] }}</td>
                                    <td class="px-4">{{ $this->yearBase[$i]['leave'] ?? '' }}</td>
                                @else
                                    <td class="px-4"></td>
                                    <td class="px-4"></td>
                                @endif

                                @if ($i < count($this->monthBase))
                                    <td class="px-4">{{ $this->monthBase[$i]['month'] }}</td>
                                    <td class="px-4">{{ $this->monthBase[$i]['leave'] ?? '' }}</td>
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
                    day</p>
                <p class="text-xs italic text-gray-700 font-bold">Based on 8-hour day</p>

                <div class="w-full flex gap-6">

                    <table class="self-start table-auto text-xs mt-4 w-[300px]">
                        <thead class="bg-gray-100 text-left">
                            <tr class="border-b border-t border-gray-200">
                                <th class="px-4 py-2 text-nowrap">Hours</th>
                                <th class="px-4 py-2 text-nowrap">Equivalent Day</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hours as $hour)
                                <tr class="text-xs hover:bg-gray-100 odd:bg-white even:bg-gray-50">
                                    <td class="px-4 py-1">{{ $hour['hour'] }}</td>
                                    <td class="px-4 py-1">{{ $hour['equiv'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-gray-500 py-3">
                                        No data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>


                    <table class="table-auto flex-1 text-xs mt-4">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left">Minutes</th>
                                <th class="px-3 py-2 text-left">Equiv. Day</th>
                                <th class="px-3 py-2 text-left">Minutes</th>
                                <th class="px-3 py-2 text-left">Equiv. Day</th>
                            </tr>
                        </thead>

                        <tbody>
                            @for ($i = 0; $i < 30; $i++)
                                <tr class="odd:bg-white even:bg-gray-50">
                                    <td class="px-3">
                                        {{ $minutesLeft[$i]['minute'] ?? '' }}
                                    </td>
                                    <td class="px-3">
                                        {{ $minutesLeft[$i]['equiv'] ?? '' }}
                                    </td>

                                    <td class="px-3">
                                        {{ $minutesRight[$i]['minute'] ?? '' }}
                                    </td>
                                    <td class="px-3">
                                        {{ $minutesRight[$i]['equiv'] ?? '' }}
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>

                </div>
            </div>

            <div class="flex-1 h-[900px] bg-white rounded-xl p-6 shadow">
                <h2 class="text-gray-700 font-bold mb-4">Table 3</h2>
                <p class="text-sm text-gray-700">Table showing conversion for <strong>leave without pay</strong>.
                </p>

                <table class="w-full text-xs border-collapse border border-gray-200 mt-4">
                    <thead class="bg-gray-100 text-left">
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-2">Day</th>
                            <th class="px-4 py-2">Value</th>
                            <th class="px-4 py-2">Day</th>
                            <th class="px-4 py-2">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $halfDays = $halfDayBaseArray;
                            $count = count($halfDays);
                            $halfCount = ceil($count / 2);
                        @endphp
                        @for ($i = 0; $i < $halfCount; $i++)
                            @php
                                $leftData = $halfDays[$i] ?? null;
                                $rightIndex = $i + $halfCount;
                                $rightData = $halfDays[$rightIndex] ?? null;
                            @endphp
                            <tr class="text-xs hover:bg-gray-100 odd:bg-white even:bg-gray-50">
                                <td class="px-4">{{ $leftData['day'] ?? '' }}</td>
                                <td class="px-4">{{ $leftData['value'] ?? '' }}</td>
                                <td class="px-4">{{ $rightData['day'] ?? '' }}</td>
                                <td class="px-4">{{ $rightData['value'] ?? '' }}</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            <div class="flex-1 bg-white rounded-xl p-6 shadow ">
                <h2 class="text-gray-700 font-bold mb-4">Leave Types List</h2>

                {{-- <table class="min-w-full table-auto text-xs mt-4">
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
                </table> --}}
                <table class="min-w-full table-auto text-xs mt-4">
                    <thead class="bg-gray-100 text-left">
                        <tr class="border-b border-t border-gray-200">
                            <th class="px-4 py-2 text-nowrap" width="30%">Abbreviation</th>
                            <th class="px-4 py-2 text-nowrap">Fullname</th>
                            <th class="px-4 py-2 text-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveTypes as $leaveType)
                            <tr class="hover:bg-gray-50">

                                <td class="px-4 py-2"
                                    wire:click="startEdit({{ $leaveType->id }}, '{{ $leaveType->abbreviation }}', '{{ $leaveType->leave_type }}')">
                                    @if ($editingId === $leaveType->id)
                                        <input type="text" wire:model.live="editAbbreviation"
                                            wire:keydown.enter="saveEdit" wire:keydown.escape="$set('editingId', null)"
                                            class="w-full text-xs p-1 border border-gray-200 rounded" />
                                    @else
                                        {{ $leaveType->abbreviation }}
                                    @endif
                                </td>

                                <td class="px-4 py-2"
                                    wire:click="startEdit({{ $leaveType->id }}, '{{ $leaveType->abbreviation }}', '{{ $leaveType->leave_type }}')">
                                    @if ($editingId === $leaveType->id)
                                        <input type="text" wire:model.live="editFullName"
                                            wire:keydown.enter="saveEdit" wire:keydown.escape="$set('editingId', null)"
                                            class="w-full text-xs p-1 border border-gray-200 rounded" />
                                    @else
                                        {{ $leaveType->leave_type }}
                                    @endif
                                </td>

                                <td class="px-4 py-2 text-nowrap whitespace-nowrap">
                                    @if ($editingId === $leaveType->id)
                                        <button wire:click="saveEdit"
                                            class="text-xs px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                            Save
                                        </button>
                                        <button wire:click="$set('editingId', null)"
                                            class="text-xs px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 ml-1">
                                            Cancel
                                        </button>
                                    @else
                                        <button
                                            wire:click="startEdit({{ $leaveType->id }}, '{{ $leaveType->abbreviation }}', '{{ $leaveType->leave_type }}')"
                                            class="text-xs px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </button>
                                        <button wire:click="deleteLeaveType({{ $leaveType->id }})"
                                            wire:confirm="Are you sure you want to delete the leave type: {{ $leaveType->abbreviation }}?"
                                            class="text-xs px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 ml-1">
                                            <i class="fa-solid fa-trash-can"></i> Delete
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-center text-gray-500">
                                    No data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="w-[400px] flex flex-col gap-10">
            <form class="w-[400px] bg-white rounded-xl p-6 shadow" wire:submit.prevent="monthBase">
                <h2 class="text-gray-700 font-bold mb-4">Table 1</h2>
                <div>
                    <label class="block text-sm text-gray-700">Annual credit (12-month equivalent)</label>
                    <input type="text" wire:model="month_base"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
                    @error('month_base')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
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


            <form class="w-[400px] bg-white rounded-xl p-6 shadow" wire:submit.prevent="halfDayBase">
                <h2 class="text-gray-700 font-bold mb-4">Table 3</h2>
                <div>
                    <label class="block text-sm text-gray-700">Without pay credit (Half-day equivalent)</label>
                    <input type="text" wire:model="half_day_base"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
                    @error('half_day_base')
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
