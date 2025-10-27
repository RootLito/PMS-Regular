<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveCredit extends Model
{
    protected $fillable = [
        'hour_day_base',
        'leave_with_pay',
        'leave_without_pay',
    ];

}
