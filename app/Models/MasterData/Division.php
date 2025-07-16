<?php

namespace App\Models\MasterData;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'code',
        'name',
        'created_at', 
        'created_by',
        'updated_by',
        'updated_at',
         'deleted_by'
    ];
    protected $table = 'm_division';
    public $timestamps = false;
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function employee()
    {
        return $this->belongsTo(employee::class,'roles_id', 'id');
    }

}
