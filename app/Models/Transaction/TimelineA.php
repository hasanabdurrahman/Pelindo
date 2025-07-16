<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimelineA extends Model
{
    use HasFactory;

    protected $table = 't_timelineA';
    protected $guarded = [];
    public $timestamps = false;

    public function tasklist()
    {
        return $this->belongsTo(TaskList::class,'project_id', 'id');
    }

    public function timeline() 
    {
        return $this->belongsTo(Timeline::class, 'transactionnumber', 'transactionnumber');
    }
}
