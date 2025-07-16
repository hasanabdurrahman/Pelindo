<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use App\Models\MasterData\Project;
use App\Models\User;

class Ticket extends Model
{
    use HasFactory;
    protected $guarded = []; 
    protected $table = 'trx_requestticket';
    protected $startdate = ['startdate'];
    protected $enddate = ['enddate'];
    public $timestamps = false;

    public function karyawan()
    {
        return $this->hasOne(User::class, 'id','karyawan_id');
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'id','project_id');
    }

    public function setDateAttribute($value)
    {
        $this->attributes['startdate'] = Carbon::parse($value)->format('m/d/Y');
        $this->attributes['enddate'] = Carbon::parse($value)->format('m/d/Y');
    }
}
