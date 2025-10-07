<?php

namespace App\Livewire;

use Livewire\Component;

class UpdateLeaveCredits extends Component
{
    public $id;
    public $startYear;
    public $endYear;
    public $years = [];

    public function mount($id)
    {
        $this->id = $id;
        $this->years = range(date('Y'), 1999);
    }


    public function render()
    {
        return view('livewire.update-leave-credits');
    }
}
