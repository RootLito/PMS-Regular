<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Signatory;
use App\Models\Assign;
use Livewire\WithPagination;

class Signatories extends Component
{
    use WithPagination;

    public $name;
    public $designation;

    public $prapared_by = '';
    public $checked_by = '';
    public $certified_by = '';
    public $funds_available = '';
    public $approved_payment = '';
    public $prepared_by2 = '';
    
    public $allSignatories;
    public $assigned;

    public $editingId = null;
    public $deletingId = null;

    public $editName = '';
    public $editDesignation = '';

    public function mount()
    {
        $this->allSignatories = Signatory::latest()->get();
        $this->assigned = Assign::with(['prepared', 'checked', 'certified', 'funds', 'approved', 'prepared2'])
            ->latest()->first(); 
    }

    protected $rules = [
        'name' => 'required|string|min:5|max:255',
        'designation' => 'required|string|min:5|max:255',
    ];

    public function save()
    {
        $this->validate();

        Signatory::create([
            'name' => $this->name,
            'designation' => $this->designation,
        ]);

        $this->dispatch('success', message: 'Signatory added!');
        $this->reset(['name', 'designation']);
        $this->resetPage();
        $this->allSignatories = Signatory::latest()->get(); 
    }

    public function startEdit($id)
    {
        $signatory = Signatory::findOrFail($id);
        $this->editingId = $id;
        $this->editName = $signatory->name;
        $this->editDesignation = $signatory->designation;
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->reset(['editName', 'editDesignation']);
    }

    public function updateSignatory()
    {
        $this->validate([
            'editName' => 'required|string|min:2|max:255',
            'editDesignation' => 'required|string|min:2|max:255',
        ]);

        $signatory = Signatory::findOrFail($this->editingId);
        $signatory->update([
            'name' => $this->editName,
            'designation' => $this->editDesignation,
        ]);

        $this->editingId = null;
        $this->reset(['editName', 'editDesignation']);
        $this->dispatch('success', message: 'Signatory updated!');
        $this->allSignatories = Signatory::latest()->get();
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

    public function deleteSignatoryConfirmed()
    {
        Signatory::findOrFail($this->deletingId)->delete();
        $this->dispatch('success', message: 'Signatory deleted!');
        $this->deletingId = null;
        $this->allSignatories = Signatory::latest()->get();
        $this->resetPage();

        if ($this->editingId === $this->deletingId) {
            $this->cancelEdit();
        }
    }

    public function saveSignatory()
    {
        $this->validate([
            'prapared_by' => 'required|exists:signatories,id', 
            'checked_by' => 'required|exists:signatories,id', 
            'certified_by' => 'required|exists:signatories,id', 
            'funds_available' => 'required|exists:signatories,id', 
            'approved_payment' => 'required|exists:signatories,id', 
            'prepared_by2' => 'required|exists:signatories,id', 
        ]);

        Assign::create([
            'prapared_by' => $this->prapared_by,
            'checked_by' => $this->checked_by,
            'certified_by' => $this->certified_by,
            'funds_available' => $this->funds_available,
            'approved_payment' => $this->approved_payment,
            'prepared_by2' => $this->prepared_by2,
        ]);

        $this->dispatch('success', message: 'New signatory assigned!');
        $this->assigned = Assign::with(['prepared', 'checked', 'certified', 'funds', 'approved', 'prepared2'])
            ->latest()->first();

        $this->reset([
            'prapared_by',
            'checked_by',
            'certified_by',
            'funds_available',
            'approved_payment',
            'prepared_by2',
        ]);
    }

    public function render()
    {
        $signatories = Signatory::latest()->paginate(6);

        return view('livewire.signatories', [
            'signatories' => $signatories,
            'allSignatories' => $this->allSignatories,
        ]);
    }
}
