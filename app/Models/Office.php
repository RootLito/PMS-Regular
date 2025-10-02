<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $table = 'regular_office';
    protected $fillable = [
        'order_no',
        'office',
    ];
}
