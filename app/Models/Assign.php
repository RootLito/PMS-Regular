<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assign extends Model
{
    protected $table = 'assign'; 
    
    protected $fillable = [
        'prapared_by',
        'checked_by',
        'certified_by',
        'funds_available',
        'approved_payment',
    ];

    public function prepared()
    {
        return $this->belongsTo(Signatory::class, 'prapared_by');
    }

    public function checked()
    {
        return $this->belongsTo(Signatory::class, 'checked_by'); 
    }

    public function certified()
    {
        return $this->belongsTo(Signatory::class, 'certified_by'); 
    }

    public function funds()
    {
        return $this->belongsTo(Signatory::class, 'funds_available'); 
    }

    public function approved()
    {
        return $this->belongsTo(Signatory::class, 'approved_payment'); 
    }
}
