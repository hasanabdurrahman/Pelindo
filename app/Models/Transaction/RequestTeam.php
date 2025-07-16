<?php

namespace App\Models\Transaction;

use App\Models\MasterData\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class RequestTeam extends Model
{
    use HasFactory;

    protected $guarded = []; 
    protected $table = 'trx_requestteam';
    protected $startdate = ['startdate'];
    protected $enddate = ['enddate'];

    use HasApiTokens, HasFactory, Notifiable;
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
