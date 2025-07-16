<?php

namespace App\Models\Transaction;

use App\Models\MasterData\Project;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TaskList extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'approve',
    //     'karyawan_id',
    //     'transactionnumber',
    //     'project_id',
    //     'timelineA_id',
    //     'progress',
    //     'description',
    //     'created_at', 
    //     'created_by',
    //     'updated_by',
    //     'updated_at',
    //     'deleted_by',
    //     'deleted_at',
    //     'approved_at',
    //     'approved_by',
    //     'deleted_status',
    //     'image',
    // ];
    protected $guarded = []; 
    protected $table = 't_tasklist';

    use HasApiTokens, HasFactory, Notifiable;
    public $timestamps = false;

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id')->where('deleted_status', 0);
    }

    public function timelineA()
    {
        return $this->belongsTo(TimelineA::class, 'timelineA_id', 'id')->where('closed', 0);
    }
}
