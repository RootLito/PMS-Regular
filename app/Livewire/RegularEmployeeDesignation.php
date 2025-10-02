<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Office;

class RegularEmployeeDesignation extends Component
{
    use WithPagination;

    public $order_no;
    public $office;
    public $search = '';
    public $editingId = null;
    public $deletingId = null;
    public $editOrderNo;
    public $editOffice;

    protected $rules = [
        'order_no' => 'required|integer',
        'office' => 'required|string|max:255',
    ];

    // For creating new records
    public function save()
    {
        $this->validate();

        Office::create([
            'order_no' => $this->order_no,
            'office' => $this->office,
        ]);

        $this->dispatch('success', message: 'Office added successfully.');
        $this->resetForm();
    }

    // For loading edit form fields
    public function edit($id)
    {
        $this->editingId = $id;
        $office = Office::findOrFail($id);

        $this->editOrderNo = $office->order_no;
        $this->editOffice = $office->office;
    }

    // Cancel editing mode and clear edit fields
    public function cancelEdit()
    {
        $this->editingId = null;
        $this->reset(['editOrderNo', 'editOffice']);
    }

    // Save updated data
    public function update()
    {
        $this->validate([
            'editOrderNo' => 'required|integer',
            'editOffice' => 'required|string|max:255',
        ]);

        $office = Office::findOrFail($this->editingId);
        $office->update([
            'order_no' => $this->editOrderNo,
            'office' => $this->editOffice,
        ]);

        $this->dispatch('success', message: 'Office updated successfully.');

        $this->cancelEdit(); // reset editing state
    }

    public function confirmDelete($id)
    {
        $this->deletingId = $id;
    }

    public function cancelDelete()
    {
        $this->deletingId = null;
    }

    public function delete()
    {
        Office::destroy($this->deletingId);
        $this->deletingId = null;
        $this->dispatch('success', message: 'Office deleted successfully.');
    }

    public function resetForm()
    {
        $this->reset(['order_no', 'office']);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $offices = Office::query()
            ->where('order_no', 'like', '%' . $this->search . '%')
            ->orWhere('office', 'like', '%' . $this->search . '%')
            ->orderBy('order_no')
            ->paginate(7);

        return view('livewire.regular-employee-designation', [
            'offices' => $offices
        ]);
    }
}
