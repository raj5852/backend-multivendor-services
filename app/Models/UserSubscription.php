<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSubscription extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [];

    function subscription(){
        return $this->belongsTo(Subscription::class,'subscription_id');
    }
}
