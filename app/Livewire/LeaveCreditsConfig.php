<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LeaveCredit;
use App\Models\LeaveType;

class LeaveCreditsConfig extends Component
{
    public $hour_day_base;
    public $month_base;


    public $hours = [];
    public $minutes = [];
    public $days = [];
    public $months = [];

    public $abbreviation;
    public $leave_type;

    public $hourly_base = [];
    public $monthly_base = [];

    public $monthBase = [];
    public $hourDayBase = [];
    public $minutesLeft = [];
    public $minutesRight = [];

    public function mount()
    {
        $config = LeaveCredit::first();
        $this->hour_day_base = array_key_last($config?->hourly_base ?? []);
        $this->month_base = array_key_last($config?->monthly_base ?? []);
        $this->fetchData();
    }
    public function hourDayBase()
    {
        $this->validate([
            'hour_day_base' => 'required|integer|min:1|max:8',
        ]);
        $hourly = $this->hour_day_base / 8;
        // HOURS
        $hours = [];
        for ($i = 1; $i <= 8; $i++) {
            $hours[$i] = number_format($hourly * $i, 3);
        }
        // MINUTES
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

        $this->dispatch('success', message: 'Monthly & Yearly base JSON saved!');
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

        $credit = LeaveCredit::first();

        if (!$credit) {
            return;
        }

        $monthly = $credit->monthly_base ?? [];
        $hourly = $credit->hourly_base ?? [];
        $minutes = $credit->minutes_base ?? [];
        $yearly = $credit->yearly_base ?? [];

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
            $equiv = number_format($i / 480, 3); // 480 = 8hrs/day
            $minuteList[] = [
                'minute' => $i,
                'equiv' => $equiv,
            ];
        }

        $this->minutesLeft = array_slice($minuteList, 0, 30);   // 1–30
        $this->minutesRight = array_slice($minuteList, 30, 30); // 31–60


        foreach ($yearly as $year => $leave) {
            $this->yearBase[] = [
                'year' => $year,
                'leave' => $leave,
            ];
        }

        // dd([
        //     'monthBase' => $this->monthBase,
        //     'hourDayBase' => $this->hourDayBase,
        //     'hours' => $this->hours,
        //     'minutesLeft' => $this->minutesLeft,
        //     'minutesRight' => $this->minutesRight,
        //     'yearBase' => $this->yearBase,
        // ]);
    }
    public function leaveTypes()
    {
        $validated = $this->validate([
            'abbreviation' => 'required|string|max:10|unique:leave_types,abbreviation',
            'leave_type' => 'required|string|max:100',
        ]);

        LeaveType::create($validated);
        $this->dispatch('success', message: 'Leave Type added successfully!');
        $this->reset(['abbreviation', 'leave_type']);
    }
    public function render()
    {
        $leaveTypes = LeaveType::all();
        return view('livewire.leave-credits-config', [
            'leaveTypes' => $leaveTypes,
            'days' => $this->hourDayBase,
            'months' => $this->monthBase,
        ]);
    }
}
