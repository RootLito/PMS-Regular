<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Office;

class RegularPayrollData extends Component
{
    public function render()
    {
        // $employees = Employee::with(['officeDetails', 'contribution'])
        // ->join('regular_office', 'regulars.office', '=', 'regular_office.office')
        // ->orderBy('regular_office.order_no')
        // ->select('regulars.*')
        // ->get()
        // ->groupBy('office');


        $employees = Employee::with(['contribution', 'officeDetails'])->get();

        $employees->each(function ($employee) {
            if ($employee->contribution) {
                $filtered = collect($employee->contribution->toArray())->filter(function ($value) {
                    return !is_null($value) && $value !== 0 && $value !== '';
                });

                $employee->filtered_contribution = (object) $filtered;
            } else {
                $employee->filtered_contribution = null;
            }
        });

        $groupedByOffice = $employees->groupBy(function ($employee) {
            return $employee->officeDetails->office ?? 'Unknown Office';
        });

        // dd($groupedByOffice);


        return view('livewire.regular-payroll-data', [
            'employeesByOffice' => $groupedByOffice,
        ]);
    }
}
