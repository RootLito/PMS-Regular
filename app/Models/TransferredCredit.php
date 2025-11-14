<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferredCredit extends Model
{
    protected $fillable = [
        'employee_id',
        'description',
        'vacation_credits',
        'sick_credits',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
