<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRecordCard extends Model
{
    protected $fillable = [
        'date_transferred',
        'employee_id',
        'period',
        'particulars',
        'particulars_type',
        'earned_vacation',
        'balance_vacation',
        'absence_w_vacation',
        'absence_wo_vacation',
        'earned_sick',
        'balance_sick',
        'absence_w_sick',
        'absence_wo_sick',
        'status',
        'remarks',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
