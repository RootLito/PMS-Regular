<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assign extends Model
{
    protected $table = 'assign';
     protected $fillable = [
        'prapared_by',
        'noted_by',
        'funds_availability',
        'approved_by',
    ];

    public function prepared()
    {
        return $this->belongsTo(Signatory::class, 'prapared_by');
    }

    public function noted()
    {
        return $this->belongsTo(Signatory::class, 'noted_by');
    }

    public function funds()
    {
        return $this->belongsTo(Signatory::class, 'funds_availability');
    }

    public function approved()
    {
        return $this->belongsTo(Signatory::class, 'approved_by');
    }
}
