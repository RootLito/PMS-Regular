<form wire:submit.prevent="update" class="space-y-4">
    <div class="flex items-center gap-2 mb-10">
        <a href="{{ route('regular-employee') }}" class="text-red-400">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h2 class="text-xl text-gray-700 font-bold">
            Update Regular Employee
        </h2>
    </div>

    <div class="w-full grid grid-cols-4 gap-2">
        <div>
            <label for="last_name" class="block text-sm text-gray-700">
                Last Name <span class="text-red-400">*</span>
            </label>
            <input type="text" id="last_name" wire:model="last_name"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
            @error('last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="first_name" class="block text-sm text-gray-700">
                First Name <span class="text-red-400">*</span>
            </label>
            <input type="text" id="first_name" wire:model="first_name"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
            @error('first_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="middle_initial" class="block text-sm text-gray-700">Middle Name</label>
            <input type="text" id="middle_initial" wire:model="middle_initial"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
            @error('middle_initial') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="suffix" class="block text-sm text-gray-700">Suffix</label>
            <input type="text" id="suffix" wire:model="suffix" maxlength="5" placeholder="e.g. Jr., Sr., III"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
            @error('suffix') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="w-full grid grid-cols-4 gap-2">
        <div>
            <label for="gender" class="block text-sm text-gray-700">
                Gender <span class="text-red-400">*</span>
            </label>
            <select id="gender" wire:model="gender"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
                <option value="" disabled>Select gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            @error('gender') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="sl_code" class="block text-sm text-gray-700">SL Code</label>
            <input type="text" id="sl_code" wire:model="sl_code"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
            @error('sl_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="position" class="block text-sm text-gray-700">
                Position <span class="text-red-400">*</span>
            </label>
            <select id="position" wire:model="position"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
                <option value="" disabled>Select a Position</option>
                @foreach ($positionOptions as $pos)
                    <option value="{{ $pos->name }}">{{ $pos->name }}</option>
                @endforeach
            </select>
            @error('position') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="office" class="block text-sm text-gray-700">Office</label>
            <select id="office" wire:model="office"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
                <option value="" disabled>Select Office</option>
                @foreach ($officeOptions as $office)
                    <option value="{{ $office->office }}">{{ $office->office }}</option>
                @endforeach
            </select>
            @error('office') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="w-full grid grid-cols-4 gap-2">
        <div>
            <label for="item_no" class="block text-sm text-gray-700">Item No <span class="text-red-400">*</span></label>
            <input type="text" id="item_no" wire:model="item_no"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
            @error('item_no') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="appointed_date" class="block text-sm text-gray-700">Appointed Date <span class="text-red-400">*</span></label>
            <input type="date" id="appointed_date" wire:model="appointed_date"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
            @error('appointed_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="salary_grade" class="block text-sm text-gray-700">
                Salary Grade <span class="text-red-400">*</span>
            </label>
            <select id="salary_grade" wire:model.live="salary_grade"
                class="mt-1 block w-full h-10 border border-gray-200 bg-white rounded-md px-2 text-sm">
                <option value="" selected>Salary Grade</option>
                @foreach ($salaryGradeOptions as $grade)
                    <option value="{{ $grade }}">{{ $grade }}</option>
                @endforeach
            </select>
            @error('salary_grade') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="step" class="block text-sm text-gray-700">
                Step <span class="text-red-400">*</span>
            </label>
            <select id="step" wire:model.live="step"
                class="mt-1 block w-full h-10 border border-gray-200 bg-white rounded-md px-2 text-sm" @if (!$salary_grade) disabled @endif>
                <option value="" selected>Select Step</option>
                @for ($i = 1; $i <= 8; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
            @error('step') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="w-full grid grid-cols-2 gap-2 mt-4">
        <div>
            <label for="monthly_rate" class="block text-sm text-gray-700">
                Monthly Rate <span class="text-red-400">*</span>
            </label>
            <input type="text" id="monthly_rate"
                wire:model.live="monthly_rate" readonly
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-100 rounded-md px-2 text-sm">
            @error('monthly_rate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="gross" class="block text-sm text-gray-700">
                Gross <span class="text-red-400">*</span>
            </label>
            <input type="number" id="gross" wire:model="gross" readonly
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
            @error('gross') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="pt-4">
        <button type="submit" class="w-full bg-slate-700 text-white py-2 rounded-md hover:bg-slate-500 cursor-pointer">
            Update
        </button>
    </div>
</form>
