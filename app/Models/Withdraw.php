<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    use HasFactory;
    protected $guarded = [];

    function affiliator(){
        return $this->belongsTo(User::class,'affiliator_id','id');
    }
}
