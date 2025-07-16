<?php

namespace App\Models\Transaction;

use App\Models\MasterData\Project;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    use HasFactory;

    protected $table = 't_timeline';
    protected $guarded = [];
    public $timestamps = false;

    public function timeline_detail(){
        return $this->hasMany(TimelineA::class, 'transactionnumber', 'transactionnumber')->where('closed', 0);
    }

    public function project(){
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}