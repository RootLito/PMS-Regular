<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Salary;

class RegularSalaryEncode extends Component
{
    use WithPagination;

    public $salary_grade;
    public $step_1;
    public $step_2;
    public $step_3;
    public $step_4;
    public $step_5;
    public $step_6;
    public $step_7;
    public $step_8;

    public $editingId = null;
    public $deletingId = null;

    public $editSalaryGrade;
    public $editStep_1;
    public $editStep_2;
    public $editStep_3;
    public $editStep_4;
    public $editStep_5;
    public $editStep_6;
    public $editStep_7;
    public $editStep_8;

    protected $rules = [
        'salary_grade' => 'required|integer|min:1|unique:regular_salaries,salary_grade',
        'step_1' => 'required|numeric|min:10000|max:999999',
        'step_2' => 'required|numeric|min:10000|max:999999',
        'step_3' => 'required|numeric|min:10000|max:999999',
        'step_4' => 'required|numeric|min:10000|max:999999',
        'step_5' => 'required|numeric|min:10000|max:999999',
        'step_6' => 'required|numeric|min:10000|max:999999',
        'step_7' => 'required|numeric|min:10000|max:999999',
        'step_8' => 'required|numeric|min:10000|max:999999',
    ];

    protected $messages = [
        '*.required' => 'This field is required.',
        'salary_grade.unique' => 'Salary grade already exists.',
    ];

    public function save()
    {
        $this->validate();

        Salary::create([
            'salary_grade' => $this->salary_grade,
            'step_1' => $this->step_1,
            'step_2' => $this->step_2,
            'step_3' => $this->step_3,
            'step_4' => $this->step_4,
            'step_5' => $this->step_5,
            'step_6' => $this->step_6,
            'step_7' => $this->step_7,
            'step_8' => $this->step_8,
        ]);

        $this->dispatch('success', message: 'Salary grade added successfully.');
        $this->reset(['salary_grade', 'step_1', 'step_2', 'step_3', 'step_4', 'step_5', 'step_6', 'step_7', 'step_8']);
    }

    public function edit($id)
    {
        $this->editingId = $id;
        $salary = Salary::findOrFail($id);

        $this->editSalaryGrade = $salary->salary_grade;
        $this->editStep_1 = $salary->step_1;
        $this->editStep_2 = $salary->step_2;
        $this->editStep_3 = $salary->step_3;
        $this->editStep_4 = $salary->step_4;
        $this->editStep_5 = $salary->step_5;
        $this->editStep_6 = $salary->step_6;
        $this->editStep_7 = $salary->step_7;
        $this->editStep_8 = $salary->step_8;

        $this->deletingId = null;
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->reset([
            'editSalaryGrade',
            'editStep_1',
            'editStep_2',
            'editStep_3',
            'editStep_4',
            'editStep_5',
            'editStep_6',
            'editStep_7',
            'editStep_8',
        ]);
    }

    public function updateSalary()
    {
        $this->validate([
            'editSalaryGrade' => 'required|integer|min:1',
            'editStep_1' => 'required|numeric|min:0',
            'editStep_2' => 'required|numeric|min:0',
            'editStep_3' => 'required|numeric|min:0',
            'editStep_4' => 'required|numeric|min:0',
            'editStep_5' => 'required|numeric|min:0',
            'editStep_6' => 'required|numeric|min:0',
            'editStep_7' => 'required|numeric|min:0',
            'editStep_8' => 'required|numeric|min:0',
        ], [
            '*.required' => 'This field is required',
        ]);

        $salary = Salary::findOrFail($this->editingId);

        $salary->update([
            'salary_grade' => $this->editSalaryGrade,
            'step_1' => $this->editStep_1,
            'step_2' => $this->editStep_2,
            'step_3' => $this->editStep_3,
            'step_4' => $this->editStep_4,
            'step_5' => $this->editStep_5,
            'step_6' => $this->editStep_6,
            'step_7' => $this->editStep_7,
            'step_8' => $this->editStep_8,
        ]);

        $this->dispatch('success', message: 'Salary grade updated successfully.');
        $this->cancelEdit();
    }

    public function confirmDelete($id)
    {
        $this->deletingId = $id;
        $this->editingId = null;
    }

    public function cancelDelete()
    {
        $this->deletingId = null;
    }

    public function deleteSalaryConfirmed()
    {
        if ($this->deletingId) {
            Salary::findOrFail($this->deletingId)->delete();
            $this->dispatch('success', message: 'Salary grade deleted successfully.');
            $this->deletingId = null;
        }
    }

    public function render()
    {
        $salaries = Salary::orderBy('salary_grade')->paginate(7);

        return view('livewire.regular-salary-encode', compact('salaries'));
    }
}
