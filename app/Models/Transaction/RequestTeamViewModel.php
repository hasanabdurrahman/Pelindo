<?php

namespace App\Models\Transaction;

use App\Models\MasterData\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestTeamViewModel extends Model
{
    use HasFactory;
    protected $table = 'Request_Team';

    public function project()
    {
        return $this->hasOne(Project::class, 'id','project_id');
    }

    public function karyawan()
    {
        return $this->hasOne(User::class, 'id','karyawan_id');
    }
}
