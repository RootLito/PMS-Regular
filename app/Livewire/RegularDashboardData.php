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
    public $sort = ''; // Public property for the filter

    public function render()
    {
        // --- 1. Load ALL employees and calculate essential data for the whole dashboard ---
        $employees = Employee::all();

        $totalEmployees = $employees->count();
        $male = $employees->where('gender', 'Male')->count();
        $female = $employees->where('gender', 'Female')->count();

        // --- 2. Calculate service length, group counts, and leave records for ALL employees ---
        
        $serviceGroups = [
            'below_5' => 0,
            'five_to_nine' => 0,
            'ten_to_fourteen' => 0,
            'fifteen_up' => 0,
        ];

        // Prepare the main employee collection (add service details and leave records)
        $employees = $employees->map(function ($emp) use (&$serviceGroups) {
            
            // Service Length Calculation & Grouping
            if ($emp->appointed_date) {
                $appointmentDate = Carbon::parse($emp->appointed_date);
                $diff = $appointmentDate->diff(now());
                $years = $diff->y;
                
                $emp->years_of_service_details = [
                    'years' => $years,
                    'months' => $diff->m
                ];
                // Total months for sorting
                $emp->service_months_for_sort = ($years * 12) + $diff->m; 
                
                // Calculate Service Groups for the Chart (using the total population)
                if ($years < 5) {
                    $serviceGroups['below_5']++;
                } elseif ($years <= 9) {
                    $serviceGroups['five_to_nine']++;
                } elseif ($years <= 14) {
                    $serviceGroups['ten_to_fourteen']++;
                } else {
                    $serviceGroups['fifteen_up']++;
                }
            } else {
                $emp->years_of_service_details = ['years' => 0, 'months' => 0];
                $emp->service_months_for_sort = 0;
            }

            // Leave Records Calculation
            $record = LeaveRecordCard::where('employee_id', $emp->id)->first();
            if ($record && is_array($record->records) && count($record->records) > 0) {
                $latest = collect($record->records)->last();
                $emp->latest_vac = $latest['balance_vacation'] ?? 0;
                $emp->latest_sick = $latest['balance_sick'] ?? 0;
            } else {
                $emp->latest_vac = 0;
                $emp->latest_sick = 0;
            }

            return $emp;
        });

        // --- 3. Filter and Sort logic ONLY for the service ranking list ---

        // Start with the full $employees collection (which now has service details/leave data)
        $rankedEmployees = $employees; 

        if (!empty($this->sort)) {
            $rankedEmployees = $rankedEmployees->filter(function ($emp) {
                $years = $emp->years_of_service_details['years'] ?? 0;
                switch ($this->sort) {
                    case '15_up':
                        return $years >= 15;
                    case '10_14':
                        return $years >= 10 && $years <= 14;
                    case '5_9':
                        return $years >= 5 && $years <= 9;
                    case 'below_5': // Use 'below_5' to match the new Blade value
                        return $years < 5;
                    default:
                        return true;
                }
            });
        }
        
        // Sort the filtered list by service length
        $rankedEmployees = $rankedEmployees->sortByDesc('service_months_for_sort');

        // --- 4. Prepare data for Net Balance Under 5K (using ALL employees) ---
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

        // --- 5. Pass data to the view ---
        return view('livewire.regular-dashboard-data', [
            'totalEmployees' => $totalEmployees, // Unfiltered
            'male' => $male,                     // Unfiltered
            'female' => $female,                 // Unfiltered
            'serviceGroups' => $serviceGroups,   // Unfiltered (Total population counts)
            'employees' => $employees,           // Full employee list (used for Leave Credits Rank)
            'rankedEmployees' => $rankedEmployees, // Filtered/Sorted list (used for Service Length Rank)
            'underFiveK' => $underFiveK,         // Unfiltered
        ]);
    }
}