<div class="flex-1 flex gap-10">
    <div class="flex-1 h-[1000px] flex flex-col gap-10">
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
                        <th class="px-4 py-2 text-nowrap">Vication Leave (WOP)</th>
                        <th class="px-4 py-2 text-nowrap">Leave Earned</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <div class="flex-1 h-[900px] bg-white rounded-xl p-6 shadow">
            <h2 class="text-gray-700 font-bold mb-4">Table 2</h2>
            <p class="text-sm text-gray-700">Table showing conversion of working hours/minutes into fractions of a day
            </p>
            <p class="text-xs italic text-gray-700 font-bold">Based on 8-hour day</p>

            <table class="min-w-full table-auto text-sm mt-4">
                <thead class="bg-gray-100 text-left">
                    <tr class="border-b border-t border-gray-200">
                        <th class="px-4 py-2 text-nowrap">Hours</th>
                        <th class="px-4 py-2 text-nowrap">Equivalent Day</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <table class="min-w-full table-auto text-sm mt-4">
                <thead class="bg-gray-100 text-left">
                    <tr class="border-b border-t border-gray-200">
                        <th class="px-4 py-2 text-nowrap">Minutes</th>
                        <th class="px-4 py-2 text-nowrap">Equiv. Day</th>
                        <th class="px-4 py-2 text-nowrap">Minutes</th>
                        <th class="px-4 py-2 text-nowrap">Equiv. Day</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
    <div class="w-[400px] flex flex-col gap-10">
        <form class="w-[400px] bg-white rounded-xl p-6 shadow">
            <h2 class="text-gray-700 font-bold mb-4">Table 1</h2>
            <div>
                <label for="middle_initial" class="block text-sm text-gray-700">Day/Month (1-30)</label>
                <input type="text" id="middle_initial" wire:model="middle_initial"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
                @error('middle_initial') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <button type="submit"
                class="w-full bg-slate-700 text-white py-2 rounded-md hover:bg-slate-500 cursor-pointer mt-2 mb-2">
                Confirm
            </button>
        </form>
        <form class="w-[400px] bg-white rounded-xl p-6 shadow">
            <h2 class="text-gray-700 font-bold mb-4">Table 2</h2>
            <div>
                <label for="middle_initial" class="block text-sm text-gray-700">Fullday (8-hour equivalent)</label>
                <input type="text" id="middle_initial" wire:model="middle_initial"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
                @error('middle_initial') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <button type="submit"
                class="w-full bg-slate-700 text-white py-2 rounded-md hover:bg-slate-500 cursor-pointer mt-2 mb-2">
                Confirm
            </button>
        </form>
    </div>
</div>