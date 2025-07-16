<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionRoles extends Model
{
    use HasFactory;
    protected $table = 'permission_role';
    protected $guarded = []; 
    public $timestamps = false;

    public function roles(){
        return $this->belongsTo(Roles::class, 'role_id', 'id');
    }
}
