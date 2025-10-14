<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $table = 'regular_salaries';
    protected $fillable = [
        'salary_grade',
        'step_1',
        'step_2',
        'step_3',
        'step_4',
        'step_5',
        'step_6',
        'step_7',
        'step_8',
    ];
}
