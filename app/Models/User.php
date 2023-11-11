<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens,
        HasFactory,
        SoftDeletes,
        Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_as',
        'number',
        'image',
        'status',
        'balance',
        'uniqid'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    function brands()
    {
        return $this->hasMany(Brand::class, 'user_id', 'id');
    }
    function getVendorBrands()
    {
        return $this->hasMany(Brand::class, 'user_id', 'id')->where('status', 'active');
    }

    function usersubscription()
    {
        return $this->hasOne(UserSubscription::class, 'user_id');
    }

    function vendorsubscription()
    {
        return $this->hasOne(UserSubscription::class, 'user_id');
    }


    function usersubscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'user_id');
    }

    function paymenthistories()
    {
        return $this->hasMany(PaymentHistory::class, 'user_id');
    }

    function affiliatoractiveproducts()
    {
        return $this->hasMany(ProductDetails::class, 'user_id');
    }

    function vendoractiveproduct()
    {
        return $this->hasMany(ProductDetails::class, 'vendor_id');
    }

    function scopeSearch($query,$value)
    {
        $query->where('email', 'like', "%{$value}%")
            ->orWhere('uniqid', 'like', "%{$value}%");
    }
}
