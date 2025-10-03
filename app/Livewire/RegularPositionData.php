<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Position;

class RegularPositionData extends Component
{
    use WithPagination;

    public string $name = '';
    public string $editName = '';
    public string $search = '';
    public $editingId = null;
    public $deletingId = null;

    protected $rules = [
        'name' => 'required|string|max:255|unique:positions,name', 
    ];

    protected $messages = [
        'name.required' => 'This field is required.',
        'name.unique' => 'This position already exists.',
    ];

    public function save()
    {
        $this->validate();

        Position::create([
            'name' => $this->name,
        ]);

        $this->dispatchBrowserEvent('success', ['message' => 'New Position added.']);
        $this->reset('name');
    }

    public function edit($id)
    {
        $this->editingId = $id;
        $position = Position::findOrFail($id);
        $this->editName = $position->name;
        $this->deletingId = null;
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->editName = '';
    }

    public function updatePosition()
    {
        $this->validate([
            'editName' => 'required|string|max:255|unique:positions,name,' . $this->editingId,
        ], [
            'editName.required' => 'This field is required.',
            'editName.unique' => 'This position already exists.',
        ]);

        $position = Position::findOrFail($this->editingId);
        $position->name = $this->editName;
        $position->save();

        $this->dispatchBrowserEvent('success', ['message' => 'Position updated successfully.']);
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

    public function deletePositionConfirmed()
    {
        if ($this->deletingId) {
            Position::find($this->deletingId)?->delete();
            $this->dispatchBrowserEvent('success', ['message' => 'Position deleted successfully.']);
            $this->deletingId = null;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $positions = Position::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(7);

        return view('livewire.regular-position-data', [
            'positions' => $positions,
        ]);
    }
}
