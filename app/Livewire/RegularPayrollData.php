<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Office;

use Carbon\Carbon;

class RegularPayrollData extends Component
{
    public $officeOptions = [];
    public array $office = [];
    public bool $showOffices = false;

    public $month;
    public $year;
    public $months = [];
    public $years = [];

    public function mount()
    {
        $this->officeOptions = Office::orderBy('order_no')->pluck('office', 'office')->values()->all();

        $this->months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        $currentYear = now()->year;
        $this->years = range($currentYear - 5, $currentYear + 5);

        $this->month = now()->month;
        $this->year = $currentYear;

        $this->office = [];
    }

    public function toggleOffices()
    {
        $this->showOffices = !$this->showOffices;
    }

    public function proceed()
    {
        $this->showOffices = false;
    }

    




    // public function render()
    // {

    //     $employees = Employee::with(['contribution', 'officeDetails'])->get();

    //     $employees->each(function ($employee) {
    //         if ($employee->contribution) {
    //             $filtered = collect($employee->contribution->toArray())->filter(function ($value) {
    //                 return !is_null($value) && $value !== 0 && $value !== '';
    //             });

    //             $employee->filtered_contribution = (object) $filtered;
    //         } else {
    //             $employee->filtered_contribution = null;
    //         }
    //     });

    //     $groupedByOffice = $employees->groupBy(function ($employee) {
    //         return $employee->officeDetails->office ?? 'Unknown Office';
    //     });


    //     return view('livewire.regular-payroll-data', [
    //         'employeesByOffice' => $groupedByOffice,
    //     ]);
    // }

    public function render()
    {
        // Query employees, optionally filtering by selected offices
        $employees = Employee::with(['contribution', 'officeDetails'])
            ->when(!empty($this->office), function ($query) {
                $query->whereHas('officeDetails', function ($q) {
                    $q->whereIn('office', $this->office);
                });
            })
            ->get();

        // Filter each employee’s contribution values
        $employees->each(function ($employee) {
            if ($employee->contribution) {
                $filtered = collect($employee->contribution->toArray())
                    ->filter(fn($value) => !is_null($value) && $value !== 0 && $value !== '');
                $employee->filtered_contribution = (object) $filtered;
            } else {
                $employee->filtered_contribution = null;
            }
        });

        // Group employees by office
        $groupedByOffice = $employees->groupBy(function ($employee) {
            return $employee->officeDetails->office ?? 'Unknown Office';
        });

        // Return the Blade view
        return view('livewire.regular-payroll-data', [
            'employeesByOffice' => $groupedByOffice,
        ]);
    }
}
