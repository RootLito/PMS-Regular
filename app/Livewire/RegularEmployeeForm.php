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
    public $monthly_rate = null; 
    public $gross = null;         
    public $item_no;
    public $appointed_date;
    public $salary_grade;
    public $step;
    public $officeOptions = [];
    public $positionOptions = [];
    public $salaryGradeOptions = [];

    public function mount()
    {
        $this->officeOptions = Office::orderBy('order_no')->get();
        $this->positionOptions = Position::orderBy('name')->get();


        $this->salaryGradeOptions = Salary::orderBy('salary_grade')
        ->pluck('salary_grade')
        ->unique()
        ->values()
        ->toArray();
    }

    public function updatedSalaryGrade()
    {
        $this->updateMonthlyRateAndGross();
    }

    public function updatedStep()
    {
        $this->updateMonthlyRateAndGross();
    }

    protected function updateMonthlyRateAndGross()
    {
        if (!is_numeric($this->salary_grade) || $this->salary_grade < 1) {
            $this->monthly_rate = null;
            $this->gross = null;
            return;
        }

        if (!in_array($this->step, range(1, 8))) {
            $this->monthly_rate = null;
            $this->gross = null;
            return;
        }

        $salary = Salary::where('salary_grade', $this->salary_grade)->first();

        if (!$salary) {
            $this->monthly_rate = null;
            $this->gross = null;
            return;
        }

        $stepColumn = 'step_' . $this->step;
        $this->monthly_rate = $salary->$stepColumn ?? null;

        if ($this->monthly_rate !== null) {
            $this->gross = round($this->monthly_rate / 2, 2);
        } else {
            $this->gross = null;
        }
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
            'item_no'         => 'required|string|max:50',
            'appointed_date'  => 'required|string',
            'salary_grade'    => 'required|integer|min:1',
            'step'            => 'required|integer|between:1,8',
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
