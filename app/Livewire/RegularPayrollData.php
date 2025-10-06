<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Office;

class RegularPayrollData extends Component
{
    public function render()
    {
        $employees = Employee::with(['officeDetails', 'contribution'])
        ->join('regular_office', 'regulars.office', '=', 'regular_office.office')
        ->orderBy('regular_office.order_no')
        ->select('regulars.*')
        ->get()
        ->groupBy('office');

            dd($employees); 

        return view('livewire.regular-payroll-data', [
            'employeesByOffice' => $employees,
        ]);
    }
}
