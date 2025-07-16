<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalTimelineA extends Model
{
    use HasFactory;

    protected $table = 't_additionalA';
    protected $guarded = [];
    public $timestamps = false;
}
