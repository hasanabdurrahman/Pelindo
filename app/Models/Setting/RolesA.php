<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolesA extends Model
{
  

    use HasFactory;
    public $timestamps = false;
    protected $table = 'm_rolesA';
    protected $with = ['menu'];
    protected $fillable = [
        'id','code','id_menu','active','xapprove','xadd','xupdate','xdelete','xprint'
    ];

    public function menu(){
        return $this->belongsTo(Menu::class,'id_menu','id')->where('deleted_status', 0);
    }

    public function roles()
    {
        return $this->hasMany(Role::class, 'code', 'code');
    }
}

