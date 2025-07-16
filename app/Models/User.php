<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Setting\Roles;
use App\Models\MasterData\Division;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\FileUploadTrait;

class User extends Authenticatable
{

    protected $table = 'm_employee';
    use HasApiTokens, HasFactory, Notifiable , FileUploadTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'email',
        'divisi_id',
        'roles_id',
        'address',
        'phone',
        'active',
        'created_at', 
        'created_by',
        'updated_by',
        'updated_at',
        'deleted_by',
        'picture',
        
    ];
    public $timestamps = false;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function roles()
    {
        return $this->belongsTo(Roles::class, 'roles_id', 'id');
    }
    public function division()
    {
        return $this->belongsTo(Division::class, 'divisi_id', 'id');
    }
}
