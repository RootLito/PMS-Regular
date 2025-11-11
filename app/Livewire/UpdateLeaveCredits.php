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
    public $leave = "";
    public $absence_undertime = "";
    public $added_period;
    public $added_vac;
    public $added_sick;
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
    // GENERATE ANNUAL CREDIT------------------------
    // public function generateAnnualCredits()
    // {
    //     $record = LeaveRecordCard::where('employee_id', $this->id)->first();

    //     if ($record && collect($record->records)->contains('period_year', $this->annualCredits)) {
    //         $this->dispatch(event: 'error', message: 'Credits for this year exist.');
    //         return;
    //     }

    //     $leaveCredit = LeaveCredit::first();
    //     $monthlyLeave = $leaveCredit->leave_with_pay / 12;

    //     $records = $record ? $record->records : [];
    //     $prevVac = collect($records)->last()['balance_vacation'] ?? 0;
    //     $prevSick = collect($records)->last()['balance_sick'] ?? 0;

    //     $entries = [];

    //     for ($month = 1; $month <= 12; $month++) {

    //         $prevVac += $monthlyLeave;
    //         $prevSick += $monthlyLeave;

    //         $firstDay = Carbon::create($this->annualCredits, $month, 1)->format('j');
    //         $lastDay = Carbon::create($this->annualCredits, $month, 1)->endOfMonth()->format('j');

    //         $entries[] = [
    //             'period_month' => $month,
    //             'period_day' => "$firstDay-$lastDay",
    //             'period_year' => $this->annualCredits,

    //             'earned_vacation' => $monthlyLeave,
    //             'balance_vacation' => $prevVac,

    //             'earned_sick' => $monthlyLeave,
    //             'balance_sick' => $prevSick,
    //         ];
    //     }

    //     if (!$record) {
    //         LeaveRecordCard::create([
    //             'employee_id' => $this->id,
    //             'records' => $entries,
    //         ]);
    //     } else {
    //         $record->records = array_merge($records, $entries);
    //         $record->save();
    //     }

    //     $this->loadData();
    //     $this->dispatch(event: 'success', message: 'Annual credits generated successfully');
    // }




    // SAVE NEW RECORD-------------------------------- 
    // public function saveRecord()
    // {
    //     $record = LeaveRecordCard::where('employee_id', $this->id)->first();
    //     if (!$record || !$record->records) {
    //         $this->dispatch(event: 'error', message: 'No annual records exist. Generate annual credits first.');
    //         return;
    //     }
    //     $leaveCredit = LeaveCredit::first();
    //     $records = collect($record->records);
    //     $previous = $records
    //         ->filter(function ($r) {
    //             if ((int) $r['period_year'] == (int) $this->period_year && (int) $r['period_month'] < (int) $this->period_month) {
    //                 return true;
    //             }
    //             if (
    //                 (int) $r['period_year'] == (int) $this->period_year &&
    //                 (int) $r['period_month'] == (int) $this->period_month
    //             ) {
    //                 $rStartDay = intval(explode('-', $r['period_day'])[0]);
    //                 $inputStartDay = intval(explode('-', $this->period_day)[0]);
    //                 return $rStartDay < $inputStartDay;
    //             }
    //             return false;
    //         })
    //         ->sort(function ($a, $b) {
    //             return ($a['period_year'] <=> $b['period_year'])
    //                 ?: ($a['period_month'] <=> $b['period_month'])
    //                 ?: (intval(explode('-', $a['period_day'])[0]) <=> intval(explode('-', $b['period_day'])[0]));
    //         })
    //         ->last();
    //     $prevVac = $previous['balance_vacation'] ?? 0;
    //     $prevSick = $previous['balance_sick'] ?? 0;
    //     $hour_day_base = $leaveCredit ? $leaveCredit->hour_day_base : 0;
    //     $leave_with_pay = $leaveCredit ? $leaveCredit->leave_with_pay : 0;
    //     $days_credit = $leaveCredit->monthly_base;
    //     $minutes_credit = $leaveCredit->minutes_base;
    //     $hours_credit = $leaveCredit->hourly_base;

    //     if ($this->leave == "vacation_leave") {
    //         $earned_vacation = 0;
    //         $day = ltrim($this->leaveDays, '0');
    //         $hour = ltrim($this->leaveHours, '0');
    //         $minutes = ltrim($this->leaveMinutes, '0');
    //         $day_equiv = isset($days_credit[$day]) ? $days_credit[$day] : 0;
    //         $hour_equiv = isset($hours_credit[$hour]) ? $hours_credit[$hour] : 0;
    //         $minute_equiv = isset($minutes_credit[$minutes]) ? $minutes_credit[$minutes] : 0;
    //         $totalLeave = $day_equiv + $hour_equiv + $minute_equiv;
    //         $wp = 0;
    //         $wop = 0;

    //         if ($this->absence_undertime == "wp") {
    //             $wp = $totalLeave;
    //         } elseif ($this->absence_undertime == "wop") {
    //             $wop = $totalLeave;
    //         }

    //         $newEntry = [
    //             'period_month' => $this->period_month,
    //             'period_day' => $this->period_day,
    //             'period_year' => (string) $this->period_year,
    //             'particulars' => [
    //                 $this->selected_leave,
    //                 "{$this->leaveDays}-{$this->leaveHours}-{$this->leaveMinutes}"
    //             ],
    //             'balance_vacation' => $prevVac - $totalLeave,
    //             'absence_w_vacation' => $wp,
    //             'absence_wo_vacation' => $wop,
    //             'balance_sick' => $prevSick,
    //             'remarks' => $this->remarks,
    //         ];
    //         // dd($day_equiv, $hour_equiv, $minute_equiv, $totalLeave, $newEntry);
    //     } elseif ($this->leave == "sick_leave") {
    //         $earned_vacation = 0;
    //         $day = ltrim($this->leaveDays, '0');
    //         $hour = ltrim($this->leaveHours, '0');
    //         $minutes = ltrim($this->leaveMinutes, '0');
    //         $day_equiv = isset($days_credit[$day]) ? $days_credit[$day] : 0;
    //         $hour_equiv = isset($hours_credit[$hour]) ? $hours_credit[$hour] : 0;
    //         $minute_equiv = isset($minutes_credit[$minutes]) ? $minutes_credit[$minutes] : 0;
    //         $totalLeave = $day_equiv + $hour_equiv + $minute_equiv;
    //         $wp = 0;
    //         $wop = 0;

    //         if ($this->absence_undertime == "wp") {
    //             $wp = $totalLeave;
    //         } elseif ($this->absence_undertime == "wop") {
    //             $wop = $totalLeave;
    //         }
    //         $newEntry = [
    //             'period_month' => $this->period_month,
    //             'period_day' => $this->period_day,
    //             'period_year' => (string) $this->period_year,
    //             'particulars' => [
    //                 $this->selected_leave,
    //                 "{$this->leaveDays}-{$this->leaveHours}-{$this->leaveMinutes}"
    //             ],
    //             'balance_sick' => $prevSick - $totalLeave,
    //             'absence_w_sick' => $wp,
    //             'absence_wo_sick' => $wop,
    //             'balance_vacation' => $prevVac,
    //             'remarks' => $this->remarks,
    //         ];
    //         // dd($day_equiv, $hour_equiv, $minute_equiv, $totalLeave, $newEntry);
    //     }

    //     // Add new entry
    //     $records->push($newEntry);

    //     // Sort everything so the new entry lands where it belongs
    //     $sorted = collect($records)->sort(function ($a, $b) {
    //         return ($a['period_year'] <=> $b['period_year'])
    //             ?: ($a['period_month'] <=> $b['period_month'])
    //             ?: (intval(explode('-', $a['period_day'])[0]) <=> intval(explode('-', $b['period_day'])[0]));
    //     })->values();

    //     // Save back
    //     LeaveRecordCard::where('employee_id', $this->id)
    //         ->update(['records' => $sorted]);


    //     $this->loadData();
    //     $this->dispatch(event: 'success', message: 'Record inserted and balances updated');
    // }

    public function generateAnnualCredits()
    {
        $record = LeaveRecordCard::where('employee_id', $this->id)->first();

        if ($record && collect($record->records)->contains('period_year', $this->annualCredits)) {
            $this->dispatch(event: 'error', message: 'Credits for this year exist.');
            return;
        }

        $leaveCredit = LeaveCredit::first();

        $monthlyBase = is_array($leaveCredit->monthly_base)
            ? $leaveCredit->monthly_base
            : json_decode($leaveCredit->monthly_base, true);

        $monthlyLeave = floatval($monthlyBase[30] ?? 0);

        $records = $record ? $record->records : [];

        $last = collect($records)->last();
        $prevVac = isset($last['balance_vacation']) ? floatval($last['balance_vacation']) : 0;
        $prevSick = isset($last['balance_sick']) ? floatval($last['balance_sick']) : 0;

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
                'earned_vacation' => round($monthlyLeave, 3),
                'balance_vacation' => round($prevVac, 3),
                'earned_sick' => round($monthlyLeave, 3),
                'balance_sick' => round($prevSick, 3),
                'generated' => true,
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
        $days_credit = $leaveCredit->hourly_base ?? [];
        $minutes_credit = $leaveCredit->minutes_base ?? [];
        $hours_credit = $leaveCredit->hourly_base ?? [];

        $totalDeducted = 0;

        if ($this->leave == "vacation_leave") {
            $day = ltrim($this->leaveDays, '0');
            $hour = ltrim($this->leaveHours, '0');
            $minutes = ltrim($this->leaveMinutes, '0');
            $day_equiv = floatval($this->leaveDays);
            $hour_equiv = isset($hours_credit[$hour]) ? $hours_credit[$hour] : 0;
            $minute_equiv = isset($minutes_credit[$minutes]) ? $minutes_credit[$minutes] : 0;
            $totalLeave = $day_equiv + $hour_equiv + $minute_equiv;

            $wp = $this->absence_undertime == "wp" ? $totalLeave : 0;
            $wop = $this->absence_undertime == "wop" ? $totalLeave : 0;

            $newEntry = [
                'generated' => false,
                'period_month' => $this->period_month,
                'period_day' => $this->period_day,
                'period_year' => (string) $this->period_year,
                'particulars' => [
                    $this->selected_leave,
                    "{$this->leaveDays}-{$this->leaveHours}-{$this->leaveMinutes}"
                ],
                'balance_vacation' => $prevVac - $totalLeave,
                'absence_w_vacation' => $wp,
                'absence_wo_vacation' => $wop,
                'balance_sick' => $prevSick,
                'remarks' => $this->remarks,
            ];
            // dd($newEntry);
        } elseif ($this->leave == "sick_leave") {
            $day = ltrim($this->leaveDays, '0');
            $hour = ltrim($this->leaveHours, '0');
            $minutes = ltrim($this->leaveMinutes, '0');
            $day_equiv = floatval($this->leaveDays);
            $hour_equiv = isset($hours_credit[$hour]) ? $hours_credit[$hour] : 0;
            $minute_equiv = isset($minutes_credit[$minutes]) ? $minutes_credit[$minutes] : 0;
            $totalLeave = $day_equiv + $hour_equiv + $minute_equiv;
            $wp = $this->absence_undertime == "wp" ? $totalLeave : 0;
            $wop = $this->absence_undertime == "wop" ? $totalLeave : 0;
            $newEntry = [
                'generated' => false,
                'period_month' => $this->period_month,
                'period_day' => $this->period_day,
                'period_year' => (string) $this->period_year,
                'particulars' => [
                    $this->selected_leave,
                    "{$this->leaveDays}-{$this->leaveHours}-{$this->leaveMinutes}"
                ],
                'balance_sick' => $prevSick - $totalLeave,
                'absence_w_sick' => $wp,
                'absence_wo_sick' => $wop,
                'balance_vacation' => $prevVac,
                'remarks' => $this->remarks,
            ];
            // dd($newEntry);
        } else {
            $this->dispatch(event: 'error', message: 'Unknown leave type.');
            return;
        }
        $records->push($newEntry);


        // dd($newEntry);




        $sortedCollection = collect($records)->sort(function ($a, $b) {
            return ($a['period_year'] <=> $b['period_year'])
                ?: ($a['period_month'] <=> $b['period_month'])
                ?: (intval(explode('-', $a['period_day'])[0]) <=> intval(explode('-', $b['period_day'])[0]));
        })->values();
        $sorted = $sortedCollection->toArray();
        $first = $sorted[0];

        $vac = floatval($first['balance_vacation'] ?? 0);
        $sick = floatval($first['balance_sick'] ?? 0);

        $sorted[0]['balance_vacation'] = $vac;
        $sorted[0]['balance_sick'] = $sick;
        for ($i = 1; $i < count($sorted); $i++) {
            $rec = $sorted[$i];
            $earnedVac = floatval($rec['earned_vacation'] ?? 0);
            $earnedSick = floatval($rec['earned_sick'] ?? 0);
            $wpVac = floatval($rec['absence_w_vacation'] ?? 0);
            $wpSick = floatval($rec['absence_w_sick'] ?? 0);

            // fixed --------------------------------
            $woSick = floatval($rec['absence_wo_sick'] ?? 0);
            $woVac = floatval($rec['absence_wo_vacation'] ?? 0);
            $vac = round($vac + $earnedVac - ($wpVac + $woVac), 3);
            $sick = round($sick + $earnedSick - ($wpSick + $woSick), 3);

            $sorted[$i]['balance_vacation'] = $vac;
            $sorted[$i]['balance_sick'] = $sick;
        }
        LeaveRecordCard::where('employee_id', $this->id)
            ->update(['records' => $sorted]);
        $this->loadData();
        $this->dispatch(event: 'success', message: 'Record inserted and balances updated');
    }
    // DELETE RECORD--------------------------------
    public function deleteRecord($index)
    {
        $record = LeaveRecordCard::where('employee_id', $this->id)->first();

        if (!$record || !$record->records) {
            $this->dispatch(event: 'error', message: 'No records to delete.');
            return;
        }

        $records = collect($record->records);

        // ✅ Remove record
        if (!isset($records[$index])) {
            $this->dispatch(event: 'error', message: 'Record not found.');
            return;
        }

        $records->forget($index);

        // ✅ If empty after delete
        if ($records->count() === 0) {
            LeaveRecordCard::where('employee_id', $this->id)->update(['records' => []]);
            $this->loadData();
            $this->dispatch(event: 'success', message: 'Record deleted.');
            return;
        }

        // ✅ SORT AGAIN by year, month, starting day
        $sortedCollection = $records
            ->sort(function ($a, $b) {
                return ($a['period_year'] <=> $b['period_year'])
                    ?: ($a['period_month'] <=> $b['period_month'])
                    ?: (intval(explode('-', $a['period_day'])[0]) <=> intval(explode('-', $b['period_day'])[0]));
            })
            ->values();

        $sorted = $sortedCollection->toArray();

        // ✅ RECALCULATE balances from the beginning
        $vac = floatval($sorted[0]['balance_vacation'] ?? 0);
        $sick = floatval($sorted[0]['balance_sick'] ?? 0);

        $sorted[0]['balance_vacation'] = $vac;
        $sorted[0]['balance_sick'] = $sick;

        for ($i = 1; $i < count($sorted); $i++) {

            $rec = $sorted[$i];

            $earnedVac = floatval($rec['earned_vacation'] ?? 0);
            $earnedSick = floatval($rec['earned_sick'] ?? 0);

            $wpVac = floatval($rec['absence_w_vacation'] ?? 0);
            $wpSick = floatval($rec['absence_w_sick'] ?? 0);

            $vac = round($vac + $earnedVac - $wpVac, 3);
            $sick = round($sick + $earnedSick - $wpSick, 3);

            $sorted[$i]['balance_vacation'] = $vac;
            $sorted[$i]['balance_sick'] = $sick;
        }

        LeaveRecordCard::where('employee_id', $this->id)
            ->update(['records' => $sorted]);

        $this->loadData();
        $this->dispatch(event: 'success', message: 'Record deleted and balances recalculated.');
    }

    public function addedCredits()
    {
        $this->validate([
            'added_period' => 'required|string|max:255',
            'added_vac' => 'required|numeric',
            'added_sick' => 'required|numeric',
        ]);

        $record = LeaveRecordCard::where('employee_id', $this->id)->first();

        if (!$record || !$record->records) {
            $this->dispatch(event: 'error', message: 'No annual records exist. Generate annual credits first.');
            return;
        }

        $records = collect($record->records);

        $newStart = [
            'period' => [$this->added_period],
            'balance_vacation' => floatval($this->added_vac),
            'balance_sick' => floatval($this->added_sick),
        ];

        // dd($newStart);  


        $records->prepend($newStart);

        $sorted = $records->values()->toArray();

        $vac = floatval($sorted[0]['balance_vacation'] ?? 0);
        $sick = floatval($sorted[0]['balance_sick'] ?? 0);

        for ($i = 1; $i < count($sorted); $i++) {
            $rec = $sorted[$i];

            $vac -= $rec['absence_w_vacation'] ?? 0;
            $sick -= $rec['absence_w_sick'] ?? 0;

            $sorted[$i]['balance_vacation'] = $vac;
            $sorted[$i]['balance_sick'] = $sick;
        }

        LeaveRecordCard::where('employee_id', $this->id)
            ->update(['records' => $sorted]);

        $this->added_period = null;
        $this->added_vac = null;
        $this->added_sick = null;

        $this->loadData();
        $this->dispatch(event: 'success', message: 'Added credits successfully and recalculated balances.');
    }




    public function deleteYear($year)
    {
        $record = LeaveRecordCard::where('employee_id', $this->id)->first();

        if (!$record || empty($record->records)) {
            return;
        }

        $remaining = collect($record->records)
            ->filter(fn($r) => (string) $r['period_year'] !== (string) $year)
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
