<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalTimeline extends Model
{
    use HasFactory;
    protected $table = 't_additional';
    protected $guarded = [];
    public $timestamps = false;

    public function timeline_detail(){
        return $this->hasMany(AdditionalTimelineA::class, 'ad_number', 'ad_number')->where('closed', 0);
    }
}
