<?php

namespace App\Models\Transaction;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryApprovalTasklist extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'h_tasklist_approval';

    public function employeeCreated(){
        return $this->hasOne(User::class, 'name', 'created_by');
    }
}
