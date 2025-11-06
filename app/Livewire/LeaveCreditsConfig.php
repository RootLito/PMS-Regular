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
    public $minutesLeft = [];
    public $minutesRight = [];
    public $days = [];
    public $months = [];

    public $abbreviation;
    public $leave_type;

    public $hourly_base = [];
    public $monthly_base = [];


    public function mount()
    {
        $config = LeaveCredit::first();



        $this->getHourDayBaseProperty();
        $this->getMonthBaseProperty();

    }

    public function hourDayBase()
    {
        $this->validate([
            'hour_day_base' => 'required|integer|min:1',
        ]);

        $result = [];
        for ($i = 1; $i <= $this->hour_day_base; $i++) {
            $result[$i] = number_format($i / $this->hour_day_base, 3);
        }

        LeaveCredit::updateOrCreate(
            ['id' => 1],
            ['hourly_base' => $result] // ✅ NO JSON_ENCODE
        );

        $this->dispatch('success', message: 'Hourly base JSON saved!');
    }


    public function monthBase()
    {
        $this->validate([
            'month_base' => 'required|integer|min:1|max:30',
        ]);

        $monthly = $this->month_base / 12;

        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            $result[$i] = number_format($monthly * $i, 3);
        }

        LeaveCredit::updateOrCreate(
            ['id' => 1],
            ['monthly_base' => $result] 
        );

        $this->dispatch('success', message: 'Monthly base JSON saved!');
    }




    public function getHourDayBaseProperty()
    {
        $credit = LeaveCredit::find(1);

        if ($credit && $credit->hourly_base) {
            $this->hourly_base = json_decode($credit->hourly_base, true);

            // dd('Hourly Base:', $this->hourly_base);
        } else {
            $this->hourly_base = [];
            // dd('Hourly Base Empty', $this->hourly_base);
        }
    }


    public function getMonthBaseProperty()
    {
        $credit = LeaveCredit::find(1);

        if ($credit && $credit->monthly_base) {
            $this->monthly_base = json_decode($credit->monthly_base, true);

            // dd('Monthly Base:', $this->monthly_base);
        } else {
            $this->monthly_base = [];
            // dd('Monthly Base Empty', $this->monthly_base);
        }
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
            'leaveTypes' => $leaveTypes
        ]);
    }
}
