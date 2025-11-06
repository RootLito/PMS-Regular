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
    public $fullname;
    public $office;
    public $appointed_date;
    public $startYear;
    public $endYear;
    public $leaveTypes;
    public $period_month;
    public $period_day;
    public $period_year;
    public $selected_leave;
    public $leaveDays;
    public $leaveHours;
    public $leaveMinutes;
    public $remarks = "";
    public $annualCredits;
    public $leave;
    public $leaveRecords = [];
    public $years = [];
    public $months = [];

    // INITIAL -------------------------------------
    public function mount($id)
    {
        $this->id = $id;
        $this->years = range(date('Y'), 1999);
        $this->leaveTypes = LeaveType::all();
        $this->startYear = 1999;
        $this->endYear = date('Y');
        $this->annualCredits = date('Y');
        $emp = Employee::findOrFail($this->id);
        $this->fullname = trim("{$emp->first_name}, {$emp->last_name} {$emp->suffix}, {$emp->middle_initial}");
        $this->office = $emp->office;
        $this->appointed_date = $emp->appointed_date;
        $this->loadData();



        $this->months = collect(range(1, 12))->map(function ($m) {
            return [
                'num' => $m,
                'name' => Carbon::create()->month($m)->format('F'),
            ];
        })->toArray();
        $this->period_month = now()->month;
        $this->period_year = now()->year;
    }


    // LOAD DATA-------------------------------------
    public function loadData()
    {
        $record = LeaveRecordCard::where('employee_id', $this->id)->first();

        if (!$record || !$record->records) {
            $this->leaveRecords = [];
            return;
        }

        $selectedYear = $this->endYear ?: now()->year;

        $records = collect($record->records)
            ->sortBy([
                ['period_year', 'asc'],
                ['period_month', 'asc'],
            ])
            ->values();

        if ($this->endYear !== "" && $this->endYear !== null) {
            $records = $records->where('period_year', (int) $selectedYear)->values();
        }

        $this->leaveRecords = $records->map(function ($r) {

            $month = Carbon::create($r['period_year'], $r['period_month'], 1);
            $start = $month->startOfMonth()->format('j');
            $end = $month->endOfMonth()->format('j');
            $periodLabel = $month->format('F') . " {$start}-{$end} {$r['period_year']}";

            return array_merge($r, [
                'period' => $periodLabel
            ]);
        })->toArray();
    }
    public function updatedEndYear()
    {
        $this->loadData();
    }

    // GENERATE ANNUAL CREDIT------------------------
    public function generateAnnualCredits()
    {
        $record = LeaveRecordCard::where('employee_id', $this->id)->first();

        if ($record && collect($record->records)->contains('period_year', $this->annualCredits)) {
            $this->dispatch(event: 'error', message: 'Credits for this year exist.');
            return;
        }

        $leaveCredit = LeaveCredit::first();
        $monthlyLeave = $leaveCredit->leave_with_pay / 12;

        $records = $record ? $record->records : [];
        $prevVac = collect($records)->last()['balance_vacation'] ?? 0;
        $prevSick = collect($records)->last()['balance_sick'] ?? 0;

        $entries = [];

        for ($month = 1; $month <= 12; $month++) {

            $prevVac += $monthlyLeave;
            $prevSick += $monthlyLeave;

            $firstDay = Carbon::create($this->annualCredits, $month, 1)->format('j');
            $lastDay = Carbon::create($this->annualCredits, $month, 1)->endOfMonth()->format('j');

            $entries[] = [
                'period_month' => $month,
                'period_day' => "$firstDay-$lastDay",
                'period_year' => $this->annualCredits,

                'earned_vacation' => $monthlyLeave,
                'balance_vacation' => $prevVac,

                'earned_sick' => $monthlyLeave,
                'balance_sick' => $prevSick,
            ];
        }

        if (!$record) {
            LeaveRecordCard::create([
                'employee_id' => $this->id,
                'records' => $entries,
            ]);
        } else {
            $record->records = array_merge($records, $entries);
            $record->save();
        }

        $this->loadData();
        $this->dispatch(event: 'success', message: 'Annual credits generated successfully');
    }




    // SAVE NEW RECORD-------------------------------- 
    public function saveRecord()
    {
        $record = LeaveRecordCard::where('employee_id', $this->id)->first();
        if (!$record || !$record->records) {
            $this->dispatch(event: 'error', message: 'No annual records exist. Generate annual credits first.');
            return;
        }

        $leaveCredit = LeaveCredit::first();
        $hour_day_base = $leaveCredit ? $leaveCredit->hour_day_base : 0;
        $leave_with_pay = $leaveCredit ? $leaveCredit->leave_with_pay : 0;
        $leave_without_pay = $leaveCredit ? $leaveCredit->leave_without_pay : 0;



        $records = collect($record->records);
        $previous = $records
            ->filter(function ($r) {
                if ((int) $r['period_year'] == (int) $this->period_year && (int) $r['period_month'] < (int) $this->period_month) {
                    return true;
                }
                if (
                    (int) $r['period_year'] == (int) $this->period_year &&
                    (int) $r['period_month'] == (int) $this->period_month
                ) {
                    $rStartDay = intval(explode('-', $r['period_day'])[0]);
                    $inputStartDay = intval(explode('-', $this->period_day)[0]);
                    return $rStartDay < $inputStartDay;
                }
                return false;
            })
            ->sort(function ($a, $b) {
                return ($a['period_year'] <=> $b['period_year'])
                    ?: ($a['period_month'] <=> $b['period_month'])
                    ?: (intval(explode('-', $a['period_day'])[0]) <=> intval(explode('-', $b['period_day'])[0]));
            })
            ->last();
        $prevVac = $previous['balance_vacation'] ?? 0;
        $prevSick = $previous['balance_sick'] ?? 0;


        dd($prevVac, $prevSick);



        $hour_day_base = $leaveCredit ? $leaveCredit->hour_day_base : 0;
        $leave_with_pay = $leaveCredit ? $leaveCredit->leave_with_pay : 0;

        // if ($this->leave == "vacation_leave") {
        //     $earned_vacation = 0;
        //     $day = $this->leaveDays;
        //     $hour = $this->leaveHours;
        //     $minutes = $this->leaveMinutes;


            
        //     $newEntry = [
        //         'period_month' => $this->period_month,
        //         'period_day' => $this->period_day,
        //         'period_year' => $this->period_year,
        //         'particulars' => $this->selected_leave,


        //         'absence_w_vacation' => $this->leaveDays,
        //         'balance_vacation' => 0,
        //         'absence_wo_vacation' => 0,

        //         'absence_w_sick' => $this->leaveDays,
        //         'balance_sick' => 0,
        //         'absence_wo_sick' => 0,

        //         'remarks' => $this->remarks,
        //     ];
        // } elseif ($this->leave == "sick_leave") {
        //     $earned_sick = 0;
        //     $day = $this->leaveDays;
        //     $hour = $this->leaveHours;
        //     $minutes = $this->leaveMinutes;

        //     $newEntry = [
        //         'period_month' => $this->period_month,
        //         'period_day' => $this->period_day,
        //         'period_year' => $this->period_year,
        //         'particulars' => $this->selected_leave,


        //         'absence_w_vacation' => $this->leaveDays,
        //         'balance_vacation' => 0,
        //         'absence_wo_vacation' => 0,

        //         'absence_w_sick' => $this->leaveDays,
        //         'balance_sick' => 0,
        //         'absence_wo_sick' => 0,

        //         'remarks' => $this->remarks,
        //     ];
        // }
$newEntry = [
                'period_month' => $this->period_month,
                'period_day' => $this->period_day,
                'period_year' => $this->period_year,
                'particulars' => $this->selected_leave,


                'absence_w_vacation' => $this->leaveDays,
                'balance_vacation' => 0,
                'absence_wo_vacation' => 0,

                'absence_w_sick' => $this->leaveDays,
                'balance_sick' => 0,
                'absence_wo_sick' => 0,

                'remarks' => $this->remarks,
            ];

        dd($newEntry);
        $this->loadData();
        $this->dispatch(event: 'success', message: 'Record inserted and balances updated');
    }



    public function render()
    {
        return view('livewire.update-leave-credits');
    }
}
