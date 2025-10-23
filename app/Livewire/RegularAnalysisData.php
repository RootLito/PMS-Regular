<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Employee;
use App\Models\Office;


class RegularAnalysisData extends Component
{
    public $search = '';
    public $office = '';
    public $sortOrder = '';
    public $offices = [];

    public function mount()
    {
        $this->offices = Office::orderBy('office')->pluck('office')->toArray();
    }


    public function render()
    {
        $employees = Employee::with('contribution')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('first_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->office, function ($query) {
                $query->where('office', $this->office);
            })
            ->when(in_array(strtolower($this->sortOrder), ['asc', 'desc']), function ($query) {
                $query->orderByRaw('LOWER(TRIM(last_name)) ' . $this->sortOrder);
            })
            ->paginate(10);

        foreach ($employees as $employee) {
            $employee->full_name = $employee->last_name
                . ', ' . $employee->first_name
                . ($employee->suffix ? ' ' . $employee->suffix : '')
                . ($employee->middle_initial ? ', ' . $employee->middle_initial : '');
        }

        // dd($employees);

        return view('livewire.regular-analysis-data', [
            'employees' => $employees,
        ]);
    }
}
