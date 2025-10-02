<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $table = 'regular_salaries';
    protected $fillable = [
        'monthly_salary',
        'gross',
    ];
}
