<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    use HasFactory;
    
    protected $table = 'm_phase';
    protected $guarded = [];
    public $timestamp = false;

    public function detail(){
        return $this->hasMany(PhaseA::class, 'phase_id', 'id')->where('deleted_status', 0)->orderby('order');
    }   
}
