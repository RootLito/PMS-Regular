<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LeaveCredit;

class LeaveCreditsConfig extends Component
{
    public $hour_day_base;
    public $leave_with_pay;
    public $leave_without_pay;


    public $hours = [];
    public $minutesLeft = [];
    public $minutesRight = [];
    public $days = [];
    public $months = [];

    public function mount()
    {
        $config = LeaveCredit::first();
        if ($config) {
            $this->hour_day_base = $config->hour_day_base;
            $this->leave_with_pay = $config->leave_with_pay;
            $this->leave_without_pay = $config->leave_without_pay;
        }


        $this->getHourDayBaseProperty();
        $this->getLeaveWithPayProperty();
    }

    public function hourDayBase()
    {
        $this->validate([
            'hour_day_base' => 'required|integer|min:1',
        ]);

        LeaveCredit::updateOrCreate(
            ['id' => 1],
            ['hour_day_base' => $this->hour_day_base]
        );

        $this->dispatch('success', message: 'Leave credits saved successfully!');
        $this->getHourDayBaseProperty();
    }

    public function leavePay()
    {
        $this->validate([
            'leave_with_pay' => 'required|integer|min:1|max:30',
            'leave_without_pay' => 'required|integer|min:1|max:30',
        ]);

        LeaveCredit::updateOrCreate(
            ['id' => 1],
            [
                'leave_with_pay' => $this->leave_with_pay,
                'leave_without_pay' => $this->leave_without_pay,
            ]
        );

        $this->dispatch('success', message: 'Leave credits saved successfully!');
        $this->getLeaveWithPayProperty();
    }


    public function getHourDayBaseProperty()
    {
        $config = LeaveCredit::first();
        $hourDayBase = $config && $config->hour_day_base > 0 ? $config->hour_day_base : 8;
        $this->hours = [];
        $this->minutesLeft = [];
        $this->minutesRight = [];
        for ($h = 1; $h <= 8; $h++) {
            $value = number_format($hourDayBase * ($h / 8), 3, '.', '');
            $this->hours[] = [
                'hour' => $h,
                'equiv' => ltrim($value, '0'),
            ];
        }
        for ($i = 1; $i <= 30; $i++) {
            $this->minutesLeft[] = [
                'minute' => $i,
                'equiv' => number_format($i / 60 * ($hourDayBase / 8), 3),
            ];
            $this->minutesRight[] = [
                'minute' => $i + 30,
                'equiv' => number_format(($i + 30) / 60 * ($hourDayBase / 8), 3),
            ];
        }
    }


    public function getLeaveWithPayProperty()
    {
        $config = LeaveCredit::first();
        $leaveWithPay = $config ? $config->leave_with_pay : 0;

        $this->days = [];
        $this->months = [];

        $leavePerMonth = $leaveWithPay / 12;
        $leavePerDay = $leavePerMonth / 30;

        for ($d = 1; $d <= 30; $d++) {
            $value = number_format($leavePerDay * $d, 3, '.', '');
            $this->days[] = [
                'day' => $d,
                'leave' => ltrim($value, '0'),
            ];
        }

        for ($m = 1; $m <= 12; $m++) {
            $value = number_format($leavePerMonth * $m, 2, '.', '');
            $this->months[] = [
                'month' => $m,
                'leave' => ltrim($value, '0'),
            ];
        }
    }





    public function render()
    {
        return view('livewire.leave-credits-config');
    }
}
