<?php

namespace App\Http\Controllers\Customer;

use App\CPU\CartManager;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\Model\Cart;
use App\Model\CartShipping;
use App\Model\ShippingCat;
use App\Model\ShippingMethod;
use App\Traits\shippingCostTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SystemController extends Controller
{
    use shippingCostTrait;
    public function set_payment_method($name)
    {
        if (auth('customer')->check() || session()->has('mobile_app_payment_customer_id')) {
            session()->put('payment_method', $name);
            return response()->json([
                'status' => 1
            ]);
        }
        return response()->json([
            'status' => 0
        ]);
    }

    public function set_shipping_method(Request $request)
    {
        if ($request['cart_group_id'] == 'all_cart_group') {
            foreach (CartManager::get_cart_group_ids() as $group_id) {
                $request['cart_group_id'] = $group_id;
                self::insert_into_cart_shipping($request);
            }
        } else {
            self::insert_into_cart_shipping($request);
        }

        return response()->json([
            'status' => 1
        ]);
    }

    public static function insert_into_cart_shipping($request)
    {
        $shipping = CartShipping::where(['cart_group_id' => $request['cart_group_id']])->first();
        if (isset($shipping) == false) {
            $shipping = new CartShipping();
        }
        $shipping['cart_group_id'] = $request['cart_group_id'];
        $shipping['shipping_method_id'] = $request['id'];
        $shipping['shipping_cost'] = ShippingMethod::find($request['id'])->cost;
        $shipping->save();
        session()->put('cart_group_id', $request['cart_group_id']);
        session()->put('shipping_cost_id', $shipping->id);
        session()->put('shipping_method_cost', ShippingMethod::find($request['id'])->cost);
    }

    public function choose_shipping_address(Request $request)
    {
        $shipping = [];
        $billing = [];
        parse_str($request->shipping, $shipping);
        parse_str($request->billing, $billing);

        if (isset($shipping['save_address']) && $shipping['save_address'] == 'on') {

            if ($shipping['contact_person_name'] == null || $shipping['address'] == null || $shipping['city'] == null ) {
                return response()->json([
                    'errors' => ['']
                ], 403);
            }

            $address_id = DB::table('shipping_addresses')->insertGetId([
                'customer_id' => auth('customer')->id(),
                'contact_person_name' => $shipping['contact_person_name'],
                'address_type' => $shipping['address_type'],
                'address' => $shipping['address'],
                'city' => $shipping['city'],
                'zip' => $shipping['zip'],
                'phone' => $shipping['phone'],
                'latitude' => $shipping['latitude'],
                'longitude' => $shipping['longitude'],
                'is_billing' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        } else if ($shipping['shipping_method_id'] == 0) {

            if ($shipping['contact_person_name'] == null || $shipping['address'] == null || $shipping['city'] == null ) {
                return response()->json([
                    'errors' => ['']
                ], 403);
            }

            $address_id = DB::table('shipping_addresses')->insertGetId([
                'customer_id' => 0,
                'contact_person_name' => $shipping['contact_person_name'],
                'address_type' => $shipping['address_type'],
                'address' => $shipping['address'],
                'city' => $shipping['city'],
                'zip' => $shipping['zip'],
                'phone' => $shipping['phone'],
                'latitude' => $shipping['latitude'],
                'longitude' => $shipping['longitude'],
                'is_billing' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $address_id = $shipping['shipping_method_id'];
        }


        if ($request->billing_addresss_same_shipping == 'false') {

            if (isset($billing['save_address_billing']) && $billing['save_address_billing'] == 'on') {

                if ($billing['billing_contact_person_name'] == null || $billing['billing_address'] == null || $billing['billing_city'] == null ) {
                    return response()->json([
                        'errors' => ['']
                    ], 403);
                }

                $billing_address_id = DB::table('shipping_addresses')->insertGetId([
                    'customer_id' => auth('customer')->id(),
                    'contact_person_name' => $billing['billing_contact_person_name'],
                    'address_type' => $billing['billing_address_type'],
                    'address' => $billing['billing_address'],
                    'city' => $billing['billing_city'],
                    'zip' => $billing['billing_zip'],
                    'phone' => $billing['billing_phone'],
                    'latitude' => $billing['billing_latitude'],
                    'longitude' => $billing['billing_longitude'],
                    'is_billing' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);


            } else if ($billing['billing_method_id'] == 0) {

                if ($billing['billing_contact_person_name'] == null || $billing['billing_address'] == null || $billing['billing_city'] == null ) {
                    return response()->json([
                        'errors' => ['']
                    ], 403);
                }

                $billing_address_id = DB::table('shipping_addresses')->insertGetId([
                    'customer_id' => 0,
                    'contact_person_name' => $billing['billing_contact_person_name'],
                    'address_type' => $billing['billing_address_type'],
                    'address' => $billing['billing_address'],
                    'city' => $billing['billing_city'],
                    'zip' => $billing['billing_zip'],
                    'phone' => $billing['billing_phone'],
                    'latitude' => $billing['billing_latitude'],
                    'longitude' => $billing['billing_longitude'],
                    'is_billing' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $billing_address_id = $billing['billing_method_id'];
            }
        } else {
            $billing_address_id = $shipping['shipping_method_id'];
        }

        session()->put('address_id', $address_id);
        session()->put('billing_address_id', $billing_address_id);

        $shipping_cost =0;
        $carts = Cart::with('product')->Where('cart_group_id' , session()->get('cart_group_id'))->get();
        $defultadress = json_decode(BusinessSetting::firstWhere('type','default_location')->value);
        foreach ($carts as $cart){
            if ($cart->product->added_by == 'admin'){
                $shipping_cat = ShippingCat::find($cart->product->shipping_cat_id);
                //return response()->json($cart->product, 500);
                if ($shipping_cat->type == 1){
                    $shipping_cost += $shipping_cat->amount * $this->getDistance($address_id,$defultadress->lat,$defultadress->lng);
                }else{
                    $shipping_cost += ($shipping_cat->amount * $cart->price * $this->getDistance($address_id,$defultadress->lat,$defultadress->lng))/100 ;
                }
            }else{
                if ($shipping_cat->type == 1){
                    $shipping_cost += $shipping_cat->amount * $this->getDistance($address_id,$cart->product->shop->latitude,$cart->product->shop->longitude);
                }else{
                    $shipping_cost += ($shipping_cat->amount * $cart->price * $this->getDistance($address_id,$cart->product->shop->latitude,$cart->product->shop->longitude))/100 ;
                }
                $shipping_cost += session()->get('shipping_method_cost') * $this->getDistance($address_id,$cart->product->shop->latitude,$cart->product->shop->longitude);
            }
        }
        $shipping = CartShipping::where('cart_group_id' , session()->get('cart_group_id'))->first();
        if (isset($shipping) == false) {
            $shipping = new CartShipping();
        }
        $shipping['cart_group_id'] = session()->get('cart_group_id');
        $shipping['shipping_method_id'] = session()->get('shipping_cost_id');
        $shipping['shipping_cost'] = $shipping_cost;
        $shipping->save();
        return response()->json([], 200);
    }

}
