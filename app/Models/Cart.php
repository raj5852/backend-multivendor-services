<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;


    public function product(){
        return $this->belongsTo('App\Models\Product');
    }

    public function colors()
    {
        return $this->belongsToMany('App\Models\Color')->withTimestamps();
    }

    public function sizes()
    {
        return $this->belongsToMany('App\Models\Size')->withTimestamps();
    }

    function cartDetails(){
        return $this->hasMany(CartDetails::class,'cart_id','id');
    }







}
