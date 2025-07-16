<?php

namespace App\Models\Transaction;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryApprovalTicket extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'h_ticket_approval';

    public function employeeCreated(){
        return $this->hasOne(User::class, 'name', 'created_by');
    }
}
