<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 
        'name', 
        'contact_person', 
        'company_phone', 
        'email',
        'company_address', 
        'created_at', 
        'created_by', 
        'updated_by',
        'updated_at', 
        'deleted_by',
        'deleted_status',
    ];
    

    public $table = 'm_client';

    public $timestamps = false;
}
