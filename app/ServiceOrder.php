<?php

namespace App;

use App\Model\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    use HasFactory;
//    protected $table = 'services';
    protected $fillable=['phone','notes'];

    public function service()
    {
        return $this->hasMany(Service::class,'service_id');
    }
}
