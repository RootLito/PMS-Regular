<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LeaveType;
use App\Models\Employee;
use App\Models\LeaveRecordCard;
use App\Models\LeaveCredit;
use Carbon\Carbon;

class UpdateLeaveCredits extends Component
{
    public $id;
    public $leaveRecords = [];
    public $fullname;
    public $office;
    public $appointed_date;
    public $startYear;
    public $endYear;
    public $leaveTypes;
    public $years = [];
    public $period;
    public $selected_leave;
    public $leaveDays;
    public $leaveHours;
    public $leaveMinutes;
    public $remarks;

    public function mount($id)
    {
        $this->id = $id;
        $this->years = range(date('Y'), 1999);
        $this->leaveTypes = LeaveType::all();
        $this->startYear = 1999;
        $this->endYear = date('Y');
        $emp = Employee::findOrFail($this->id);
        $this->fullname = trim("{$emp->first_name}, {$emp->last_name} {$emp->suffix}, {$emp->middle_initial}");
        $this->office = $emp->office;
        $this->appointed_date = $emp->appointed_date;
        $leaveRecord = LeaveRecordCard::where('employee_id', $id)->first();


        // dd($this->appointed_date);

        $appointedYear = Carbon::parse($this->appointed_date)->year;
        $currentYear = now()->year;

        if (!$leaveRecord) {
            $leaveCredit = LeaveCredit::first();
            $leaveWithPay = $leaveCredit->leave_with_pay;
            $monthlyLeave = $leaveWithPay / 12;
            $balanceVacation = 0;
            $balanceSick = 0;

            for ($year = $appointedYear; $year <= $currentYear; $year++) {
                for ($month = 1; $month <= 12; $month++) {

                    if ($year == $appointedYear && $month < Carbon::parse($this->appointed_date)->month) {
                        continue;
                    }

                    $startDate = Carbon::create($year, $month, 1);
                    $endDate = $startDate->copy()->endOfMonth();
                    $daysInMonth = $endDate->day;

                    $period = "{$startDate->format('F')} 1-{$daysInMonth} {$year}";

                    $earnedVacation = $monthlyLeave;
                    $earnedSick = $monthlyLeave;

                    $balanceVacation += $earnedVacation;
                    $balanceSick += $earnedSick;

                    LeaveRecordCard::create([
                        'employee_id' => $id,
                        'period' => $period,
                        'earned_vacation' => $earnedVacation,
                        'balance_vacation' => $balanceVacation,
                        'earned_sick' => $earnedSick,
                        'balance_sick' => $balanceSick,
                        'status' => 'Active',
                    ]);
                }
            }

            $this->dispatch(event: 'warning', message: 'No data, credits auto generate');
        }


        $this->loadData();
    }

    public function loadData()
    {
        $this->leaveRecords = LeaveRecordCard::where('employee_id', $this->id)
            ->orderByRaw("RIGHT(period, 4) ASC")
            ->orderByRaw("
            FIELD(
                SUBSTRING_INDEX(period, ' ', 1),
                'January','February','March','April','May','June',
                'July','August','September','October','November','December'
            )
        ")
            ->get();
    }



    public function saveRecord()
    {
        $this->validate([
            'period' => 'required|string',
            'selected_leave' => 'required|string',
            'leaveDays' => 'required|numeric|digits:1|min:0',
            'leaveHours' => 'nullable|numeric|digits:2|min:00|max:07',
            'leaveMinutes' => 'nullable|numeric|digits:2|min:00|max:59',
            'remarks' => 'nullable|string|max:255',
        ]);


        $period = $this->period;

        $particulars = "{$this->leaveDays}-{$this->leaveHours}-{$this->leaveMinutes}";


        dd(LeaveRecordCard::create([
            'employee_id' => $this->id,
            'period' => $period,
            'particulars' => $particulars,
            'particulars_type' => $this->selected_leave,
            'remarks' => $this->remarks,
            'earned_vacation' => 0,
            'balance_vacation' => 0,
            'earned_sick' => 0,
            'balance_sick' => 0,
            'status' => 'Active',
        ]));

        

        $this->dispatch(event: 'success', message: 'Leave record saved successfully!');
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.update-leave-credits');
    }
}
