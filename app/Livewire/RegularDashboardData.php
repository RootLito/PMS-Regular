<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;
use App\Models\LeaveRecordCard;
use App\Models\Contribution;
use Carbon\Carbon;

class RegularDashboardData extends Component
{
    public function render()
    {
        $employees = Employee::all();

        $totalEmployees = $employees->count();
        $male = $employees->where('gender', 'Male')->count();
        $female = $employees->where('gender', 'Female')->count();

        $serviceGroups = [
            'below_5' => 0,
            'five_to_nine' => 0,
            'ten_to_fourteen' => 0,
            'fifteen_up' => 0,
        ];

        foreach ($employees as $emp) {
            if (!$emp->appointed_date)
                continue;

            $emp->years_of_service = (int) Carbon::parse($emp->appointed_date)->diffInYears(now());
            $years = $emp->years_of_service;
            if ($years < 5) {
                $serviceGroups['below_5']++;
            } elseif ($years <= 9) {
                $serviceGroups['five_to_nine']++;
            } elseif ($years <= 14) {
                $serviceGroups['ten_to_fourteen']++;
            } else {
                $serviceGroups['fifteen_up']++;
            }

            $record = LeaveRecordCard::where('employee_id', $emp->id)->first();
            if ($record && is_array($record->records) && count($record->records) > 0) {
                $latest = collect($record->records)->last();
                $emp->latest_vac = $latest['balance_vacation'] ?? 0;
                $emp->latest_sick = $latest['balance_sick'] ?? 0;
            } else {
                $emp->latest_vac = 0;
                $emp->latest_sick = 0;
            }
        }

        $underFiveK = Contribution::with('employee')
            ->where('total_net_amount', '<', 5000)
            ->get()
            ->map(function ($c) {
                $mi = '';
                if ($c->employee->middlename) {
                    $mi = strtoupper(substr($c->employee->middlename, 0, 1)) . '.';
                } elseif ($c->employee->middle_initial) {
                    $mi = $c->employee->middle_initial . '.';
                }

                return [
                    'name' => strtoupper($c->employee->lastname) . ', ' .
                        strtoupper($c->employee->firstname) . ' ' .
                        $mi,
                    'net' => $c->total_net_amount
                ];
            });

        return view('livewire.regular-dashboard-data', [
            'totalEmployees' => $totalEmployees,
            'male' => $male,
            'female' => $female,
            'serviceGroups' => $serviceGroups,
            'employees' => $employees,
            'underFiveK' => $underFiveK,
        ]);
    }
}
