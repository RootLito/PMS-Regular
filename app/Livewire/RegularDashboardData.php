<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;
use App\Models\LeaveRecordCard;
use App\Models\Contribution;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class RegularDashboardData extends Component
{
    public $sort = '';



    public function render()
    {
        $employees = Employee::all();

        $totalEmployees = $employees->count();
        $male = $employees->where('gender', 'Male')->count();
        $female = $employees->where('gender', 'Female')->count();
        
        $employees = $employees->sortByDesc(function ($emp) {
            if (!$emp->appointed_date) {
                return 0;
            }
            
            $appointmentDate = Carbon::parse($emp->appointed_date);
            $diff = $appointmentDate->diff(now());
            
            return ($diff->y * 12) + $diff->m;
        });

        $serviceGroups = [
            'below_5' => 0,
            'five_to_nine' => 0,
            'ten_to_fourteen' => 0,
            'fifteen_up' => 0,
        ];

        foreach ($employees as $emp) {
            if (!$emp->appointed_date)
                continue;

            $appointmentDate = Carbon::parse($emp->appointed_date);
            $diff = $appointmentDate->diff(now());
            $emp->years_of_service_details = [
                'years' => $diff->y,
                'months' => $diff->m
            ];
            $years = $diff->y;
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
                $emp = $c->employee;
                $mi = '';
                if ($emp->middlename) {
                    $mi = strtoupper(substr($emp->middlename, 0, 1)) . '.';
                } elseif ($emp->middle_initial) {
                    $mi = strtoupper(substr($emp->middle_initial, 0, 1)) . '.';
                }
                $suffix = '';
                if (!empty($emp->suffix)) {
                    $suffix = ' ' . strtoupper($emp->suffix);
                }
                return [
                    'name' => strtoupper($emp->last_name) . ', ' .
                        strtoupper($emp->first_name) .
                        $suffix . ' ' .
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