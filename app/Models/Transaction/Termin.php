<?php

namespace App\Models\Transaction;

use App\Models\MasterData\Project;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Termin extends Model
{
    use HasFactory;

    protected $table = 't_termin';
    protected $guarded = [];
    public $timestamps = false;

    public function termin_detail(){
        return $this->hasMany(TerminA::class, 'transactionnumber', 'transactionnumber');
    }

    public function project_detail(){
        return $this->hasOne(Project::class, 'id', 'project_id');
    }
}
