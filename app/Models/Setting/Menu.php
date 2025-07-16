<?php

namespace App\Models\Setting;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'm_menu';
    protected $guarded = []; 
    public $timestamps = false;

    public function rolesA(){
        return $this->belongsTo(RolesA::class,'id','id_menu')->where('deleted_status', 0);
    }

    public function permission(){
        return $this->belongsTo(Permission::class, 'xurl', 'name')->where('deleted_status', 0);
    }

    public function created_user(){
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function updated_user(){
        return $this->hasOne(User::class, 'id', 'updated_by');
    }

    public function inactived_user(){
        return $this->hasOne(User::class, 'id', 'deleted_by');
    }
}
