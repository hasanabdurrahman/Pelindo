<?php

namespace App\Models\MasterData;

use App\Models\Setting\Roles;
use App\Models\Transaction\Timeline;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Model
{
    use HasFactory;
    
    // protected $fillable = [
    //     'code',
    //     'name',
    //     'email',
    //     'divisi_id',
    //     'roles_id',
    //     'address',
    //     'phone',
    //     'active',
    //     'password',
    //     'created_at', 
    //     'created_by',
    //     'updated_by',
    //     'updated_at',
    //     'deleted_by',
    // ];

    protected $guarded = []; 
    protected $table = 'm_employee';
    use HasApiTokens, HasFactory, Notifiable;
    public $timestamps = false;

    public function roles()
    {
        return $this->belongsTo(Roles::class, 'roles_id', 'id');
    }
}
