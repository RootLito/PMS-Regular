<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveCredit extends Model
{
    protected $fillable = [
        'hourly_base',
        'monthly_base',
    ];

    protected $casts = [
        'hourly_base' => 'array',
        'monthly_base' => 'array',
    ];
}

