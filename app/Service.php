<?php

namespace App;
use App\Model\Product;
use App\Model\Review;
use App\Model\Seller;
use App\Model\Shop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Service extends Model
{
    use HasFactory;
    protected $table = 'services';
    protected $guarded = [];
        public function scopeActive()
    {
        return $this->where(['status' => 1])->sellerApproved();
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'seller_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'user_id');
    }

//    public function scopeActive($query)
//    {
//        return $query->whereHas('brand', function ($query) {
//            $query->where(['status' => 1]);
//        })->where(['status' => 1])->sellerApproved();
//    }

    public function scopeSellerApproved($query)
    {
        $query->whereHas('seller', function ($query) {
            $query->where(['status' => 'approved']);
        })->orWhere(function ($query) {
            $query->where(['added_by' => 'admin', 'status' => 1])->where(['status' => 1]);
                });
    }
//    public function scopeSellerApproved($query)
//    {
//        $query->whereHas('seller', function ($query) {
//            $query->where(['status' => 'approved']);
//        })->orWhere(function ($query) {
//            $query->where(['added_by' => 'admin', 'status' => 1]);
//
//        });
//    }

    public function serviceorder()
    {
        return $this->belongsTo(ServiceOrder::class, 'service_id');
    }

    public function translations()
    {
        return $this->morphMany('App\Model\Translation', 'translationable');
    }

//    public function scopeActive()
//    {
//        return $this->where('status', 1);
//    }

    public function reviews()
    {
        return $this->hasMany(Review::class)
            ->select(DB::raw('avg(rating) average, service_id'))
            ->groupBy('service_id');
    }
}
