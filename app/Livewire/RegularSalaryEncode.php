<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Salary;

class RegularSalaryEncode extends Component
{
    use WithPagination;

    public $monthly_salary;
    public $gross;
    public $search = '';

    public $editingId = null;
    public $deletingId = null;

    public $editMonthlySalary;
    public $editGross;

    protected $rules = [
        'monthly_salary' => 'required | numeric|min:0 | unique:regular_salaries,monthly_salary',
        'gross' => 'required | numeric',
    ];

    protected $messages = [
        'monthly_salary.unique' => 'Salary already encoded.',
    ];

    public function save()
    {
        $this->validate();

        $calculatedGross = $this->monthly_salary / 2;

        Salary::create([
            'monthly_salary' => $this->monthly_salary,
            'gross' => $calculatedGross,
        ]);

        $this->dispatch('success', message: 'Salary added successfully.');
        $this->reset(['monthly_salary', 'gross']);
    }
    public function updatedMonthlySalary($value)
    {
        if (empty($value)) {
            $this->gross = null;
        } else {
            $this->gross = ((float) $value) / 2;
        }
    }



    public function edit($id)
    {
        $this->editingId = $id;
        $salary = Salary::findOrFail($id);
        $this->editMonthlySalary = $salary->monthly_salary;
        $this->editGross = $salary->gross;
        $this->deletingId = null;
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->editMonthlySalary = '';
        $this->editGross = '';
    }

    public function updateSalary()
    {
        $this->validate([
            'editMonthlySalary' => 'required|numeric|min:0',
            'editGross' => 'required|string|max:255',
        ]);

        $salary = Salary::findOrFail($this->editingId);
        $salary->update([
            'monthly_salary' => $this->editMonthlySalary,
            'gross' => $this->editGross,
        ]);

        $this->dispatch('success', message: 'Salary updated successfully.');
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
            $this->dispatch('success', message: 'Salary deleted successfully.');
            $this->deletingId = null;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $salaries = Salary::query()
            ->where('monthly_salary', 'like', '%' . $this->search . '%')
            ->orWhere('gross', 'like', '%' . $this->search . '%')
            ->orderBy('monthly_salary')
            ->paginate(7);

        return view('livewire.regular-salary-encode', [
            'salaries' => $salaries,
        ]);
    }
}
