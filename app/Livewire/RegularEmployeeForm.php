<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Salary;
use App\Models\Office;
use Livewire\Component;
use App\Models\Position;

class RegularEmployeeForm extends Component
{
    public $last_name;
    public $first_name;
    public $middle_initial;
    public $suffix;
    public $gender = '';
    public $sl_code;
    public $office = '';
    public $position = '';
    public $monthly_rate = '';
    public $gross;
    public $officeOptions = [];
    public $positionOptions = [];
    public $monthlyRateOptions = [];

    public function mount()
    {
        $this->officeOptions = Office::orderBy('office')->get();
        $this->positionOptions = Position::orderBy('name')->get();
        $this->monthlyRateOptions = Salary::orderBy('monthly_salary')->get();
    }

    public function updatedMonthlyRate($value)
    {
        $this->gross = is_numeric($value) ? number_format($value / 2, 2, '.', '') : null;
    }



    public function save()
    {
        $validatedData = $this->validate([
            'last_name'       => 'required|string|max:100',
            'first_name'      => 'required|string|max:100',
            'middle_initial'  => 'nullable|string|max:100',
            'suffix'          => 'nullable|string|max:10',
            'gender'          => 'required|string',
            'position'        => 'required|string',
            'office'          => 'required|string',
            'monthly_rate'    => 'required|numeric',
            'gross'           => 'required|numeric',
            'sl_code'         => 'required|string|max:50',
        ], [
            '*.required' => 'This field is required.',
        ]);

        Employee::create($validatedData);

        $this->dispatch('success', message: 'Employee added.');
        $this->reset();
        $this->mount();
    }


    public function render()
    {
        return view('livewire.regular-employee-form');
    }
}
