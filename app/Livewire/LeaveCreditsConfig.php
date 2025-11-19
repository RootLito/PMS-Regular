<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LeaveCredit;
use App\Models\LeaveType;

class LeaveCreditsConfig extends Component
{
    public $hour_day_base;
    public $month_base;
    public $half_day_base;
    public $halfDayBaseArray = [];
    public $hours = [];
    public $minutes = [];
    public $days = [];
    public $months = [];
    public $abbreviation;
    public $leave_type;
    public $hourly_base = [];
    public $monthly_base = [];
    public $yearly_base = [];
    public $monthBase = [];
    public $hourDayBase = [];
    public $minutesLeft = [];
    public $minutesRight = [];
    public $yearBase = [];

    public $leaveTypes;
    public $editingId = null;
    public $editAbbreviation = '';
    public $editFullName = '';

    public function mount()
    {
        $this->leaveTypes = LeaveType::all();
        $config = LeaveCredit::first();
        $hourlyBase = $config?->hourly_base ?? [];
        $yearlyBase = $config?->yearly_base ?? [];
        $halfDayBase = $config?->half_day_base ?? [];
        $this->hour_day_base = !empty($hourlyBase) ? end($hourlyBase) : null;
        $this->month_base = !empty($yearlyBase) ? end($yearlyBase) : null;
        $this->half_day_base = !empty($halfDayBase) ? reset($halfDayBase) : null;
        $this->halfDayBaseArray = is_array($halfDayBase) ? $halfDayBase : json_decode($halfDayBase, true) ?? [];
        $this->fetchData();
    }

    public function hourDayBase()
    {
        $this->validate([
            'hour_day_base' => 'required|integer|min:1|max:8',
        ]);
        $hourly = $this->hour_day_base / 8;
        $hours = [];
        for ($i = 1; $i <= 8; $i++) {
            $hours[$i] = number_format($hourly * $i, 3);
        }
        $minutes = [];
        for ($m = 1; $m <= 59; $m++) {
            $minutes[$m] = number_format(($hourly / 60) * $m, 3);
        }
        LeaveCredit::updateOrCreate(
            ['id' => 1],
            [
                'hourly_base' => $hours,
                'minutes_base' => $minutes
            ]
        );
        $this->dispatch('success', message: 'Hourly and Minutes base saved!');
        $this->fetchData();
    }

    public function halfDayBase()
    {
        $startValue = $this->half_day_base;
        $endValue = 0.000;
        $fullRangeDays = 30.00;
        $stepSize = 0.50;
        $slope = ($startValue - $endValue) / $fullRangeDays;

        $half_day_base = [];

        for ($D = 0; $D <= $fullRangeDays; $D += $stepSize) {
            $value = $startValue - ($slope * $D);
            $key = number_format($D, 2, '.', '');
            $half_day_base[$key] = number_format(max(0, $value), 3, '.', '');
        }

        LeaveCredit::updateOrCreate(
            ['id' => 1],
            ['half_day_base' => $half_day_base]
        );

        $this->halfDayBaseArray = $half_day_base;
        $this->dispatch('success', message: 'Leave without pay conversion saved!');
        $this->fetchData();
    }


    public function leaveTypes()
    {
        dd(123);

        $validated = $this->validate([
            'abbreviation' => 'required|string|max:10|unique:leave_types,abbreviation',
            'leave_type' => 'required|string|max:100',
        ]);
        LeaveType::create($validated);
        $this->dispatch('success', message: 'Leave Type added successfully!');
        $this->reset(['abbreviation', 'leave_type']);
    }

    public function monthBase()
    {
        $this->validate([
            'month_base' => 'required|integer|min:1|max:30',
        ]);
        $perMonth = $this->month_base / 12;
        $perDay = $perMonth / 30;

        $yearly_base = [];
        for ($i = 1; $i <= 12; $i++) {
            $yearly_base[$i] = number_format($perMonth * $i, 3);
        }
        $monthly_base = [];
        for ($i = 1; $i <= 30; $i++) {
            $monthly_base[$i] = number_format($perDay * $i, 3);
        }
        LeaveCredit::updateOrCreate(
            ['id' => 1],
            [
                'monthly_base' => $monthly_base,
                'yearly_base' => $yearly_base,
            ]
        );
        $this->dispatch('success', message: 'Leave without pay conversion saved!');
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->monthBase = [];
        $this->hourDayBase = [];
        $this->hours = [];
        $this->minutesLeft = [];
        $this->minutesRight = [];
        $this->yearBase = [];
        $this->halfDayBaseArray = [];

        $credit = LeaveCredit::first();
        if (!$credit) {
            return;
        }
        $monthly = $credit->monthly_base ?? [];
        $hourly = $credit->hourly_base ?? [];
        $minutes = $credit->minutes_base ?? [];
        $yearly = $credit->yearly_base ?? [];
        $halfDayBase = $credit->half_day_base ?? [];
        $halfDayData = is_array($halfDayBase) ? $halfDayBase : json_decode($halfDayBase, true) ?? [];
        $transformedHalfDays = [];
        foreach ($halfDayData as $day => $value) {
            $transformedHalfDays[] = [
                'day' => $day,
                'value' => $value
            ];
        }
        $this->halfDayBaseArray = $transformedHalfDays;
        foreach ($monthly as $month => $leave) {
            $this->monthBase[] = [
                'month' => $month,
                'leave' => $leave,
            ];
        }
        foreach ($hourly as $day => $leave) {
            $this->hourDayBase[] = [
                'day' => $day,
                'leave' => $leave,
            ];
        }
        foreach ($hourly as $hour => $leave) {
            $this->hours[] = [
                'hour' => $hour,
                'equiv' => $leave,
            ];
        }
        $minuteList = [];
        for ($i = 1; $i <= 60; $i++) {
            $equiv = number_format($i / 480, 3);
            $minuteList[] = [
                'minute' => $i,
                'equiv' => $equiv,
            ];
        }
        $this->minutesLeft = array_slice($minuteList, 0, 30);
        $this->minutesRight = array_slice($minuteList, 30, 30);
        foreach ($yearly as $year => $leave) {
            $this->yearBase[] = [
                'year' => $year,
                'leave' => $leave,
            ];
        }
    }



    public function startEdit($id, $abbr, $full)
    {
        $this->editingId = $id;
        $this->editAbbreviation = $abbr;
        $this->editFullName = $full;
    }

    public function deleteLeaveType($id)
    {
        try {
            $leaveType = LeaveType::findOrFail($id);
            $leaveType->delete();
            $this->leaveTypes = LeaveType::all();
            $this->dispatch('success', message: 'Leave type deleted successfully.');
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to delete leave type: ' . $e->getMessage());
        }
    }

    public function saveEdit()
    {
        $this->validate([
            'editAbbreviation' => 'required|max:10',
            'editFullName' => 'required|max:100',
        ]);

        $leaveType = LeaveType::find($this->editingId);
        if ($leaveType) {
            $leaveType->abbreviation = $this->editAbbreviation;
            $leaveType->leave_type = $this->editFullName;
            $leaveType->save();
        }

        $this->editingId = null;
        $this->leaveTypes = LeaveType::all();
        $this->dispatch('success', message: 'Leave type updated.');
    }

    public function render()
    {
        $leaveTypes = LeaveType::all();
        return view('livewire.leave-credits-config', [
            'leaveTypes' => $leaveTypes,
            'days' => $this->hourDayBase,
            'months' => $this->monthBase,
            'halfDayBaseArray' => $this->halfDayBaseArray,
        ]);
    }
}
