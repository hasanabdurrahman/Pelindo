<?php

namespace App\Models\MasterData;

use App\Models\Transaction\TaskList;
use App\Models\Transaction\Termin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction\Timeline;
use PDO;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'id_client',
        'contract_number',
        'value',
        'startdate',
        'enddate',
        'pc_id',
        'sales_id',
        'xtype',
        'description',
        'created_at',
        'created_by',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
    ];
    protected $table = 'm_project';
    public $timestamps = false;

    public function tasklist()
    {
        return $this->belongsTo(TaskList::class,'project_id', 'id');
    }
    public function timelines()
    {
        return $this->hasMany(Timeline::class, 'project_id', 'id');
    }
    public function client(){
        return $this->hasOne(Client::class, 'id', 'id_client');
    }
    public function pc(){
        return $this->hasOne(Employee::class, 'id', 'pc_id');
    }
    public function sales(){
        return $this->hasOne(Employee::class, 'id', 'sales_id');
    }
    public function termin(){
        return $this->hasOne(Termin::class, 'project_id', 'id');
    }
}
