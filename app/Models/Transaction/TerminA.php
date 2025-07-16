<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TerminA extends Model
{
    use HasFactory;

    protected $table = 't_terminA';
    protected $guarded = [];
    public $timestamps = false;

    // public function termin() 
    // {
    //     return $this->belongsTo(Termin::class, 'transactionnumber', 'transactionnumber');
    // }
}
