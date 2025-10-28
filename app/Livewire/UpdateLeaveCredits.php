<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LeaveType;

class UpdateLeaveCredits extends Component
{
    public $id;
    public $startYear;
    public $endYear;
    public $leaveTypes;
    public $years = [];

    public function mount($id)
    {
        $this->id = $id;
        $this->years = range(date('Y'), 1999);


        
        $this->leaveTypes = LeaveType::all();

        $this->startYear = 1999;
        $this->endYear = date('Y');
    }


    public function render()
    {
        return view('livewire.update-leave-credits');
    }
}
