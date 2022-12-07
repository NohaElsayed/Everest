<?php

namespace App\Model;

use App\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Seller extends Authenticatable
{
    use HasRoles;
    use Notifiable;
    protected $guarded = [];  
    protected $guard_name = 'seller';
    protected $casts = [
        'id' => 'integer',
        'orders_count' => 'integer',
        'product_count' => 'integer',
        'pos+status' => 'integer'
    ];

    public function scopeApproved($query)
    {
        return $query->where(['status'=>'approved']);
    }

    public function shop()
    {
        return $this->hasOne(Shop::class, 'seller_id');
    }

    public function shops()
    {
        return $this->hasMany(Shop::class, 'seller_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function product()
    {
        return $this->hasMany(Product::class, 'user_id')->where(['added_by'=>'seller']);
    }
    public function service()
    {
        return $this->hasMany(Service::class, 'user_id')->where(['added_by'=>'seller']);
    }

    public function wallet()
    {
        return $this->hasOne(SellerWallet::class);
    }

}
