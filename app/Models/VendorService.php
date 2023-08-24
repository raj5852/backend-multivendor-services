<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorService extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $casts = [
        'tags' => 'json'
    ];
    function servicepackages()
    {
        return $this->hasMany(ServicePackage::class);
    }

    function serviceimages()
    {
        return $this->hasMany(ServiceImages::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($query) {
            $query->servicepackages()->delete();
            $query->serviceimages()->delete();
        });
    }
}
