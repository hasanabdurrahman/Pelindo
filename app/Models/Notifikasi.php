<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_read',
        'update_at',
        'update_by',
    ];
    protected $table = 'notifikasi';
    protected $guarded = [];
    public $timestamp = false;
}
