<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveCredit extends Model
{
    protected $fillable = [
        'hourly_base',
        'minutes_base',
        'monthly_base',
        'yearly_base',
    ];


    protected $casts = [
        'hourly_base' => 'array',
        'minutes_base' => 'array',
        'monthly_base' => 'array',
        'yearly_base' => 'array',
    ];
}

