<?php

namespace App\Traits;

use App\Model\Seller;
use App\Model\ShippingAddress;
use App\Model\Shop;

trait shippingCostTrait
{
    public function getDistance($shipping_address_id,$shopLatitude,$shopLongitude){
        $customer = ShippingAddress::find($shipping_address_id);
        $radius = 6371; // radius of the Earth
        $dLatCustomer = $customer->latitude * M_PI/180;
        $dLatSeller = $shopLatitude * M_PI/180;
        $deltaLat = ($customer->latitude - $shopLatitude) * M_PI/180;
        $deltaLong = ($customer->longitude - $shopLongitude) * M_PI/180;
        $a = sin($deltaLat/2) * sin($deltaLat/2) +
            cos($dLatCustomer) * cos($dLatSeller) *
              sin($deltaLong/2) * sin($deltaLong/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
       return $radius * $c; // Distance in km
    }
}
