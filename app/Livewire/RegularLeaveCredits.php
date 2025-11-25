<?php

namespace App\Livewire;


use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Employee;
use App\Models\Office;
use App\Models\LeaveRecordCard;

class RegularLeaveCredits extends Component
{
    use WithPagination;

    public $search = '';
    public $office = '';
    public $sortOrder = '';
    public $deletingId = null;
    public $offices = [];

    public function mount()
    {
        $this->offices = Office::orderBy('office')->pluck('office')->toArray();
    }


    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingOffice()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->deletingId = $id;
    }

    public function cancelDelete()
    {
        $this->deletingId = null;
    }

    public function deleteEmployeeConfirmed()
    {
        Employee::findOrFail($this->deletingId)->delete();
        $this->deletingId = null;
        $this->dispatch('success', message: 'Employee deleted.');
    }

    public function render()
    {
        $employees = Employee::query()
            ->with('leaveRecordCard')
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

        foreach ($employees as $emp) {
            $card = $emp->leaveRecordCard;
            if ($card && !empty($card->records)) {
                $records = collect($card->records);
                $last = $records->last(); 
                $emp->balance_vacation = $last['balance_vacation'] ?? 0;
                $emp->balance_sick = $last['balance_sick'] ?? 0;
                // $emp->balance_vacation = 0;
                // $emp->balance_sick = 0;
            }
        }


        return view('livewire.regular-leave-credits', [
            'employees' => $employees,
        ]);
    }

}
