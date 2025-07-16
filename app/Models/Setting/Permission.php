<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $table = 'permissions';
    protected $guarded = []; 
    public $timestamps = false;

    public function permission_role(){
        return $this->belongsTo(PermissionRoles::class, 'id', 'permission_id');
    }
}
