<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'regulars';
    protected $fillable = [
        'last_name',
        'first_name',
        'middle_initial',
        'suffix',
        'office',
        'position',
        'monthly_rate',
        'gross',
        'gender',
        'sl_code',
    ];
}
