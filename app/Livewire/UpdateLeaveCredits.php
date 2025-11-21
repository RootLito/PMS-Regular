<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LeaveType;
use App\Models\Employee;
use App\Models\LeaveRecordCard;
use App\Models\TransferredCredit;
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
    public $leave = "";
    public $leave_deduction = "";
    public $absence_undertime;
    public $added_period;
    public $added_vac;
    public $added_sick;
    public $leaveRecords = [];
    public $years = [];
    public $months = [];
    public $transferredCredits = [];



    protected $rules = [
        'leave' => 'required',
        'period_month' => 'required',
        'period_day' => 'required',
        'period_year' => 'required',
        'selected_leave' => 'required',
        
        'leaveDays' => 'required|numeric|digits_between:1,2|min:0|max:31',
        'leaveHours' => 'required|numeric|digits:2|min:0|max:7',
        'leaveMinutes' => 'required|numeric|digits:2|min:0|max:59',
        
        'leave_deduction' => 'required',
    ];





    // INITIAL -------------------------------------
    public function mount($id)
    {
        $this->id = $id;
        $this->years = range(date('Y'), 1999);
        $this->leaveTypes = LeaveType::all();
        $this->startYear = 1999;
        $this->endYear = "";
        $this->annualCredits = 1999;
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
        $this->transferredCredits = TransferredCredit::where('employee_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->get();
        if (!$record || !$record->records) {
            $this->leaveRecords = [];
            return;
        }
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
            $periodLabel = $month->format('F') . " {$r['period_day']} {$r['period_year']}";
            return array_merge($r, [
                'period' => $periodLabel,
            ]);
        })->toArray();
    }
    public function updatedEndYear()
    {
        $this->loadData();
    }
    // GENERATE CREDITS------------------------
    public function generateAnnualCredits()
    {
        $employee = Employee::find($this->id);
        if (!$employee || !$employee->appointed_date) {
            $this->dispatch(event: 'error', message: 'Employee appointed date not found.');
            return;
        }
        $appointedDate = Carbon::parse($employee->appointed_date);

        $currentYear = Carbon::now()->year;
        $endDate = Carbon::now()->endOfYear();

        $record = LeaveRecordCard::where('employee_id', $this->id)->first();
        $records = $record ? $record->records : [];

        $lastGenerated = collect($records)
            ->where('generated', true)
            ->sortBy(function ($r) {
                return ($r['period_year'] * 100) + $r['period_month'];
            })
            ->last();

        // --- START YEAR CHECK
        if ($lastGenerated) {
            if ((int) $lastGenerated['period_year'] == $currentYear && (int) $lastGenerated['period_month'] == 12) {
                $this->dispatch(event: 'error', message: "Credits are already generated.");
                return;
            }
        }

        $prevVac = 0;
        $prevSick = 0;

        if ($lastGenerated) {
            $prevVac = floatval($lastGenerated['balance_vacation']);
            $prevSick = floatval($lastGenerated['balance_sick']);

            $date = Carbon::create($lastGenerated['period_year'], $lastGenerated['period_month'], 1)->addMonth()->day(1);

            if ($date->year == $currentYear && $date->month > 12) {
                $date = Carbon::create($currentYear + 1, 1, 1);
            }

        } else {
            $addedVac = TransferredCredit::where('employee_id', $this->id)->sum('vacation_credits');
            $addedSick = TransferredCredit::where('employee_id', $this->id)->sum('sick_credits');
            if ($addedVac > 0 || $addedSick > 0) {
                $prevVac = $addedVac;
                $prevSick = $addedSick;
                $date = $appointedDate->copy();
            } else {
                $last = collect($records)->last();
                $prevVac = isset($last['balance_vacation']) ? floatval($last['balance_vacation']) : 0;
                $prevSick = isset($last['balance_sick']) ? floatval($last['balance_sick']) : 0;
                $date = $appointedDate->copy();
            }
        }

        $leaveCredit = LeaveCredit::first();
        $monthlyBase = is_array($leaveCredit->monthly_base)
            ? $leaveCredit->monthly_base
            : json_decode($leaveCredit->monthly_base, true);
        $fullMonthCredit = end($monthlyBase);
        $entries = [];

        if (!isset($date)) {
            $date = $appointedDate->copy();
        }


        while ($date->lessThanOrEqualTo($endDate)) {

            $exists = collect($records)->contains(function ($r) use ($date) {
                return ($r['period_year'] == $date->year && $r['period_month'] == $date->month && ($r['generated'] ?? false) === true);
            });

            if (!$exists) {
                $firstDay = ($date->month == $appointedDate->month && $date->year == $appointedDate->year)
                    ? $appointedDate->day
                    : 1;
                $lastDay = $date->copy()->endOfMonth()->day;

                if ($firstDay != 1) {
                    $workedDays = $lastDay - $appointedDate->day + 1;
                    $earnedVacation = $monthlyBase[$workedDays] ?? round(($workedDays / $lastDay) * $fullMonthCredit, 3);
                    $earnedSick = $earnedVacation;
                } else {
                    $earnedVacation = $fullMonthCredit;
                    $earnedSick = $fullMonthCredit;
                }

                $prevVac += $earnedVacation;
                $prevSick += $earnedSick;

                $entries[] = [
                    'period_month' => $date->month,
                    'period_day' => "$firstDay-$lastDay",
                    'period_year' => $date->year,
                    'earned_vacation' => round($earnedVacation, 3),
                    'balance_vacation' => round($prevVac, 3),
                    'earned_sick' => round($earnedSick, 3),
                    'balance_sick' => round($prevSick, 3),
                    'generated' => true,
                ];
            }

            $date->addMonth()->day(1);
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
        $this->dispatch(event: 'success', message: 'Credits generated up to the end of the current year successfully.');
    }























    // SAVE NEW RECORD------------------------
    public function saveRecord()
    {
        $this->validate();
        $record = LeaveRecordCard::where('employee_id', $this->id)->first();
        if (!$record || !$record->records) {
            $this->dispatch(event: 'error', message: 'No annual records exist. Generate annual credits first.');
            return;
        }
        $leaveCredit = LeaveCredit::first();
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
                $sortKeyA = intval($a['period_year']) * 10000 + intval($a['period_month']) * 100 + intval(explode('-', $a['period_day'])[0]);
                $sortKeyB = intval($b['period_year']) * 10000 + intval($b['period_month']) * 100 + intval(explode('-', $b['period_day'])[0]);
                return $sortKeyA <=> $sortKeyB;
            })
            ->last();
        $prevVac = floatval($previous['balance_vacation'] ?? 0);
        $prevSick = floatval($previous['balance_sick'] ?? 0);

        $minutes_credit = $leaveCredit->minutes_base ?? [];
        $hours_credit = $leaveCredit->hourly_base ?? [];
        $half_day_base = $leaveCredit->half_day_base ?? [];

        $getEarnedCredit = function ($woAbsence) use ($half_day_base) {
            if (empty($half_day_base) || $woAbsence <= 0) {
                return 0.00;
            }

            $woAbsence = round($woAbsence, 2);
            $closestKey = null;
            $minDifference = PHP_FLOAT_MAX;

            foreach ($half_day_base as $key => $value) {
                $keyFloat = floatval($key);
                $difference = abs($keyFloat - $woAbsence);

                if ($difference < $minDifference) {
                    $minDifference = $difference;
                    $closestKey = $key;
                }
                if ($difference == 0) {
                    break;
                }
            }

            return $closestKey !== null ? floatval($half_day_base[$closestKey]) : 0.00;
        };

        $hour = ltrim($this->leaveHours, '0');
        $minutes = ltrim($this->leaveMinutes, '0');
        $day_equiv = floatval($this->leaveDays);
        $hour_equiv = isset($hours_credit[$hour]) ? $hours_credit[$hour] : 0;
        $minute_equiv = isset($minutes_credit[$minutes]) ? $minutes_credit[$minutes] : 0;
        $totalLeave = $day_equiv + $hour_equiv + $minute_equiv;

        $totalDed = 0;
        if (in_array(strtoupper(trim($this->selected_leave)), ['VL', 'FL', 'SL', 'T', 'UT', 'TU'])) {
            $totalDed = $totalLeave;
        }

        $newEntry = [
            'generated' => false,
            'period_month' => (int) $this->period_month,
            'period_day' => $this->period_day,
            'period_year' => (int) $this->period_year,
            'particulars' => [
                $this->selected_leave,
                "{$this->leaveDays}-{$this->leaveHours}-{$this->leaveMinutes}"
            ],
            'remarks' => $this->remarks,
            'earned_vacation' => 0,
            'earned_sick' => 0,
            'absence_w_vacation' => 0,
            'absence_wo_vacation' => 0,
            'absence_w_sick' => 0,
            'absence_wo_sick' => 0,
            'balance_vacation' => $prevVac,
            'balance_sick' => $prevSick,
        ];



        // ***************************************************
        // ** THE CORE LOGIC CHANGE STARTS HERE **
        // ***************************************************

        $deductionType = $this->leave_deduction;

        if ($this->leave == "vacation_leave") {
            if ($deductionType == "with_pay") {

                if ($prevVac < $totalDed) {
                    $this->dispatch(event: 'error', message: 'Not enough Vacation Leave balance for With Pay deduction.');
                    return;
                }
                $newEntry['balance_vacation'] = $prevVac - $totalDed;
                $newEntry['absence_w_vacation'] = $totalDed;
                $newEntry['earned_vacation'] = 0;
            } elseif ($deductionType == "without_pay") {
                $newEntry['absence_wo_vacation'] = $totalDed;
                $earnedVac = $getEarnedCredit($totalDed);
                $newEntry['earned_vacation'] = $earnedVac;
            }
        } elseif ($this->leave == "sick_leave") {
            if ($deductionType == "with_pay") {
                if ($prevSick < $totalDed) {
                    $this->dispatch(event: 'error', message: 'Not enough Sick Leave balance for With Pay deduction.');
                    return;
                }
                $newEntry['balance_sick'] = $prevSick - $totalDed;
                $newEntry['absence_w_sick'] = $totalDed;
                $newEntry['earned_sick'] = 0;
            } elseif ($deductionType == "without_pay") {
                $newEntry['absence_wo_sick'] = $totalDed;
                $earnedSick = $getEarnedCredit($totalDed);
                $newEntry['earned_sick'] = $earnedSick;
            }
        } else {
            $this->dispatch(event: 'error', message: 'Unknown leave type.');
            return;
        }

        // ***************************************************
        // ** THE CORE LOGIC CHANGE ENDS HERE **
        // ***************************************************




        $records->push($newEntry);

        $sortedCollection = collect($records)->sort(function ($a, $b) {
            $aDay = intval(explode('-', $a['period_day'])[0]);
            $bDay = intval(explode('-', $b['period_day'])[0]);

            $aGenerated = $a['generated'];
            $bGenerated = $b['generated'];
            if ($a['period_year'] <=> $b['period_year']) {
                return $a['period_year'] <=> $b['period_year'];
            }
            if ($a['period_month'] <=> $b['period_month']) {
                return $a['period_month'] <=> $b['period_month'];
            }
            if ($aDay <=> $bDay) {
                return $aDay <=> $bDay;
            }
            return $aGenerated <=> $bGenerated;
        })->values();
        $sorted = $sortedCollection->toArray();
        $first = $sorted[0];

        $vac = floatval($first['balance_vacation'] ?? 0);
        $sick = floatval($first['balance_sick'] ?? 0);
        $woVac = floatval($first['absence_wo_vacation'] ?? 0);
        $woSick = floatval($first['absence_wo_sick'] ?? 0);

        $sorted[0]['balance_vacation'] = $vac;
        $sorted[0]['balance_sick'] = $sick;
        $sorted[0]['absence_wo_vacation'] = $woVac;
        $sorted[0]['absence_wo_sick'] = $woSick;

        for ($i = 1; $i < count($sorted); $i++) {
            $rec = $sorted[$i];

            $earnedVac = floatval($rec['earned_vacation'] ?? 0);
            $earnedSick = floatval($rec['earned_sick'] ?? 0);

            $wpVac = floatval($rec['absence_w_vacation'] ?? 0);
            $wpSick = floatval($rec['absence_w_sick'] ?? 0);

            $woVac = floatval($rec['absence_wo_vacation'] ?? 0);
            $woSick = floatval($rec['absence_wo_sick'] ?? 0);

            $leaveType = strtoupper(trim($rec['particulars'][0] ?? ''));

            $vac = round($vac + $earnedVac, 3);

            if (in_array($leaveType, ['VL', 'FL', 'SL', 'T', 'UT', 'TU']) && $wpVac > 0) {
                if ($vac >= $wpVac) {
                    $vac = round($vac - $wpVac, 3);
                    $actualPaidVac = $wpVac;
                } else {
                    $actualPaidVac = 0;
                }
            } else {
                $actualPaidVac = 0;
            }

            $sick = round($sick + $earnedSick, 3);

            if (in_array($leaveType, ['VL', 'FL', 'SL', 'T', 'UT', 'TU']) && $wpSick > 0) {
                if ($sick >= $wpSick) {
                    $sick = round($sick - $wpSick, 3);
                    $actualPaidSick = $wpSick;
                } else {
                    $actualPaidSick = 0;
                }
            } else {
                $actualPaidSick = 0;
            }

            $sorted[$i]['balance_vacation'] = $vac;
            $sorted[$i]['balance_sick'] = $sick;
            $sorted[$i]['absence_wo_vacation'] = $woVac;
            $sorted[$i]['absence_wo_sick'] = $woSick;

            $sorted[$i]['absence_w_vacation'] = $actualPaidVac;
            $sorted[$i]['absence_w_sick'] = $actualPaidSick;
        }

        LeaveRecordCard::where('employee_id', $this->id)
            ->update(['records' => $sorted]);
        $this->loadData();
        $this->dispatch(event: 'success', message: 'Record inserted and balances updated');
    }

























    // DELETE RECORD------------------------
    public function deleteRecord($index)
    {
        $record = LeaveRecordCard::where('employee_id', $this->id)->first();
        if (!$record || !$record->records) {
            $this->dispatch(event: 'error', message: 'No records to delete.');
            return;
        }
        $records = collect($record->records);
        if (!isset($records[$index])) {
            $this->dispatch(event: 'error', message: 'Record not found.');
            return;
        }
        $records->forget($index);
        if ($records->count() === 0) {
            LeaveRecordCard::where('employee_id', $this->id)->update(['records' => []]);
            $this->loadData();
            $this->dispatch(event: 'success', message: 'Record deleted.');
            return;
        }
        $sortedCollection = $records
            ->sort(function ($a, $b) {
                return ($a['period_year'] <=> $b['period_year'])
                    ?: ($a['period_month'] <=> $b['period_month'])
                    ?: (intval(explode('-', $a['period_day'])[0]) <=> intval(explode('-', $b['period_day'])[0]));
            })
            ->values();
        $sorted = $sortedCollection->toArray();
        $first = $sorted[0];
        $vac = floatval($first['balance_vacation'] ?? 0);
        $sick = floatval($first['balance_sick'] ?? 0);
        $sorted[0]['balance_vacation'] = $vac;
        $sorted[0]['balance_sick'] = $sick;
        $sorted[0]['absence_wo_vacation'] = floatval($first['absence_wo_vacation'] ?? 0);
        $sorted[0]['absence_wo_sick'] = floatval($first['absence_wo_sick'] ?? 0);
        for ($i = 1; $i < count($sorted); $i++) {
            $rec = $sorted[$i];
            $earnedVac = floatval($rec['earned_vacation'] ?? 0);
            $earnedSick = floatval($rec['earned_sick'] ?? 0);
            $wpVac = floatval($rec['absence_w_vacation'] ?? 0);
            $wpSick = floatval($rec['absence_w_sick'] ?? 0);
            $woVac = floatval($rec['absence_wo_vacation'] ?? 0);
            $woSick = floatval($rec['absence_wo_sick'] ?? 0);
            $leaveType = strtoupper(trim($rec['particulars'][0] ?? ''));
            if (in_array($leaveType, ['VL', 'FL', 'SL', 'T', 'UT', 'TU'])) {
                $vac = round($vac + $earnedVac - $wpVac, 3);
                $sick = round($sick + $earnedSick - $wpSick, 3);
            } else {
                $vac = round($vac + $earnedVac, 3);
                $sick = round($sick + $earnedSick, 3);
            }
            $sorted[$i]['balance_vacation'] = $vac;
            $sorted[$i]['balance_sick'] = $sick;
            $sorted[$i]['absence_wo_vacation'] = $woVac;
            $sorted[$i]['absence_wo_sick'] = $woSick;
        }

        LeaveRecordCard::where('employee_id', $this->id)
            ->update(['records' => $sorted]);
        $this->loadData();
        $this->dispatch(event: 'success', message: 'Record deleted and balances recalculated.');
    }
    // ADD TRANSFERRED CREDIT -----------------------------
    public function addedCredits()
    {
        $this->validate([
            'added_period' => 'required|string',
            'added_vac' => 'nullable|numeric',
            'added_sick' => 'nullable|numeric',
        ]);
        TransferredCredit::updateOrCreate([
            'employee_id' => $this->id,
            'description' => $this->added_period,
            'vacation_credits' => $this->added_vac ?: 0,
            'sick_credits' => $this->added_sick ?: 0,
        ]);
        $record = LeaveRecordCard::where('employee_id', $this->id)->first();
        if ($record && $record->records) {
            $records = $record->records;
            $addedVac = TransferredCredit::where('employee_id', $this->id)->sum('vacation_credits');
            $addedSick = TransferredCredit::where('employee_id', $this->id)->sum('sick_credits');
            $records[0]['balance_vacation'] = round(
                floatval($records[0]['earned_vacation'] ?? 0)
                - floatval(($records[0]['absence_w_vacation'] ?? 0) + ($records[0]['absence_wo_vacation'] ?? 0))
                + $addedVac,
                3
            );
            $records[0]['balance_sick'] = round(
                floatval($records[0]['earned_sick'] ?? 0)
                - floatval(($records[0]['absence_w_sick'] ?? 0) + ($records[0]['absence_wo_sick'] ?? 0))
                + $addedSick,
                3
            );
            $vac = $records[0]['balance_vacation'];
            $sick = $records[0]['balance_sick'];
            for ($i = 1; $i < count($records); $i++) {
                $r = $records[$i];
                $vac = round(
                    $vac
                    + floatval($r['earned_vacation'] ?? 0)
                    - floatval(($r['absence_w_vacation'] ?? 0) + ($r['absence_wo_vacation'] ?? 0)),
                    3
                );
                $sick = round(
                    $sick
                    + floatval($r['earned_sick'] ?? 0)
                    - floatval(($r['absence_w_sick'] ?? 0) + ($r['absence_wo_sick'] ?? 0)),
                    3
                );
                $records[$i]['balance_vacation'] = $vac;
                $records[$i]['balance_sick'] = $sick;
            }
            LeaveRecordCard::where('employee_id', $this->id)
                ->update(['records' => $records]);
        }
        $this->loadData();
        $this->reset(['added_period', 'added_vac', 'added_sick']);
        $this->dispatch(event: 'success', message: 'Added credits and recalculated balances.');
    }
    // DELETE TRANSFERRED CREDIT -----------------------------
    public function deleteTransferred($id)
    {
        $transfer = TransferredCredit::where('employee_id', $this->id)
            ->first();
        if (!$transfer) {
            $this->dispatch(event: 'error', message: 'No transferred record found.');
            return;
        }
        $transfer->delete();
        $record = LeaveRecordCard::where('employee_id', $this->id)->first();
        if ($record && $record->records) {
            $records = $record->records;
            $addedVac = TransferredCredit::where('employee_id', $this->id)->sum('vacation_credits');
            $addedSick = TransferredCredit::where('employee_id', $this->id)->sum('sick_credits');
            $records[0]['balance_vacation'] = round(
                floatval($records[0]['earned_vacation'] ?? 0)
                - floatval(($records[0]['absence_w_vacation'] ?? 0) + ($records[0]['absence_wo_vacation'] ?? 0))
                + $addedVac,
                3
            );
            $records[0]['balance_sick'] = round(
                floatval($records[0]['earned_sick'] ?? 0)
                - floatval(($records[0]['absence_w_sick'] ?? 0) + ($records[0]['absence_wo_sick'] ?? 0))
                + $addedSick,
                3
            );
            $vac = $records[0]['balance_vacation'];
            $sick = $records[0]['balance_sick'];
            for ($i = 1; $i < count($records); $i++) {
                $r = $records[$i];
                $vac = round(
                    $vac
                    + floatval($r['earned_vacation'] ?? 0)
                    - floatval(($r['absence_w_vacation'] ?? 0) + ($r['absence_wo_vacation'] ?? 0)),
                    3
                );
                $sick = round(
                    $sick
                    + floatval($r['earned_sick'] ?? 0)
                    - floatval(($r['absence_w_sick'] ?? 0) + ($r['absence_wo_sick'] ?? 0)),
                    3
                );
                $records[$i]['balance_vacation'] = $vac;
                $records[$i]['balance_sick'] = $sick;
            }
            LeaveRecordCard::where('employee_id', $this->id)
                ->update(['records' => $records]);

        }
        $this->loadData();
        $this->dispatch(event: 'success', message: 'Transferred credit deleted and balances recalculated.');
    }
    //  DELETE GENERATED CREDIT -----------------------------
    public function deleteYear($year)
    {
        $record = LeaveRecordCard::where('employee_id', $this->id)->first();
        if (!$record || empty($record->records)) {
            return;
        }
        $remaining = collect($record->records)
            ->filter(fn($r) => (int) $r['period_year'] !== (int) $year)
            ->values();
        $vacationBalance = 0;
        $sickBalance = 0;
        $recomputed = $remaining->map(function ($rec) use (&$vacationBalance, &$sickBalance) {
            $earned_vac = floatval($rec['earned_vacation'] ?? 0);
            $absence_vac = floatval($rec['absence_w_vacation'] ?? 0);
            $earned_sick = floatval($rec['earned_sick'] ?? 0);
            $absence_sick = floatval($rec['absence_w_sick'] ?? 0);
            $vacationBalance = $vacationBalance + $earned_vac - $absence_vac;
            $sickBalance = $sickBalance + $earned_sick - $absence_sick;
            $rec['balance_vacation'] = $vacationBalance == 0 ? "" : $vacationBalance;
            $rec['balance_sick'] = $sickBalance == 0 ? "" : $sickBalance;
            return $rec;
        })->toArray();
        $record->records = $recomputed;
        $record->save();
        $this->loadData();
        $this->dispatch('success', message: "Year {$year} deleted and balances recomputed.");
    }
    public function render()
    {
        return view('livewire.update-leave-credits');
    }
}
