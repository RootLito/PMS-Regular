<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRecordCard extends Model
{
    protected $fillable = [
        'employee_id',
        'date_transferred',
        'records',
    ];

    protected $casts = [
        'records' => 'array', 
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
