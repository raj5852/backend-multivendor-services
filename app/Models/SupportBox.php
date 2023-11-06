<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportBox extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    function user()
    {
        return $this->belongsTo(User::class);
    }

    function ticketreplay()
    {
        return $this->hasMany(TicketReply::class);
    }
    function latestTicketreplay(){
        return $this->hasOne(TicketReply::class,'support_box_id')->latestOfMany();
    }

}
