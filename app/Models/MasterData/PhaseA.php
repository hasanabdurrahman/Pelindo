<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhaseA extends Model
{
    use HasFactory;
        
    protected $table = 'm_phaseA';
    protected $guarded = [];
    public $timestamp = false;
}
