<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MasterData\Project;
use App\Models\User;

class TicketView extends Model
{
    use HasFactory;

    protected $table = 'Request_Ticket';

    public function project()
    {
        return $this->hasOne(Project::class, 'id','project_id');
    }

    public function karyawan()
    {
        return $this->hasOne(User::class, 'id','karyawan_id');
    }
}
