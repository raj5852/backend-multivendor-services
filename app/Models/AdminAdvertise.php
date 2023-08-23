<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminAdvertise extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [];

    public function AdvertiseAudienceFile()
    {
        return $this->hasMany(AdvertiseAudienceFile::class, 'advertise_id');
    }
    public function advertisePlacement()
    {
        return $this->hasMany(AdvertisePlacement::class, 'advertise_id');
    }
    public function advertiseLocationFiles()
    {
        return $this->hasMany(LocationFile::class,'advertise_id');
    }
}
