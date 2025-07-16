<?php

namespace App\Models\Setting;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Roles extends Model
{
    protected $table = 'm_roles';
    protected $guarded = []; 
    public $timestamps = false;
    use HasFactory;

    protected $dates = ['inactive_date'];

    public function rolesA()
    {
        return $this->belongsTo(RolesA::class, 'code', 'code');
    }

    public function user(){
        return $this->hasMany(User::class, 'code', 'code');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'code', 'id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'roles_id', 'id');
    }
}
