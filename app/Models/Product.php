<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;



    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }


    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id', 'id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }



    public function colors()
    {
        return $this->belongsToMany('App\Models\Color','color_product','product_id','color_id')->withPivot('qnt');
    }



    public function sizes()
    {
        return $this->belongsToMany('App\Models\Size')->withTimestamps();
    }

    public function productImage()
    {
        return $this->hasMany('App\Models\ProductImage');
    }

    public function productdetails()
    {
        return $this->hasMany('App\Models\ProductDetails');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    function specifications(){
        return $this->hasMany(specification::class,'product_id','id');
    }
    protected $casts = [
        'tags'=>'array'
    ];

}
