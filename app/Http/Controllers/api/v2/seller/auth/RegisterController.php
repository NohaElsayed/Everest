<?php

namespace App\Http\Controllers\api\v2\seller\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CPU\ImageManager;
use App\Model\Shop;
use App\Model\Zone;
use App\Model\Seller;
use App\SubscriptionSeller;
use App\Model\Category;
use App\CPU\Helpers;
use Illuminate\Support\Str;
use App\CentralLogics\CentraLs;
use App\Model\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Grimzy\LaravelMysqlSpatial\Types\Point;
class RegisterController extends Controller
{
    public function get_coordinates($id){
        $zone=Zone::withoutGlobalScopes()->selectRaw("*,ST_AsText(ST_Centroid(`coordinates`)) as center")->findOrFail($id);
        // $data = CentraLs::format_coordiantes($zone->coordinates[0]);
        $data = [];
        foreach ($zone->coordinates[0] as $coord) {
            $data[] = (object)['lat' => $coord->getlat(), 'lng' => $coord->getlng()];
        }
        // return $data;
        $center = (object)['lat'=>(float)trim(explode(' ',$zone->center)[1], 'POINT()'), 'lng'=>(float)trim(explode(' ',$zone->center)[0], 'POINT()')];
        return response()->json(['coordinates'=>$data, 'center'=>$center]);
    }
    public function register(Request $request)
    {
      //  return $request;
         $validator = Validator::make($request->all(), [ 
            // 'latitude' => 'required',
            // 'longitude' => 'required',
          //  'subscription' => 'required',
            // 'zone_id' => 'required',
        ], [
            'email' => 'required|unique:sellers',
            'shop_address' => 'required',
         //   'category_id' => 'required',
            'f_name' => 'required',
            'l_name' => 'required',
            'phone' => 'required',
            'subscription' => 'required',
            'password' => 'required|min:8',
        ]);
        return $request;
        // if($request->zone_id)
        // {
        //     $point = new Point($request->latitude, $request->longitude);
        //     $zone = Zone::contains('coordinates', $point)->where('id', $request->zone_id)->first();
        //     if(!$zone){
        //         $validator->getMessageBag()->add('latitude', trans('messages.coordinates_out_of_zone'));
        //         return back()->withErrors($validator)
        //                 ->withInput();
        //     }
        // }
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        return $request;

         DB::transaction(function ($r) use ($request) {
            $auth_token = Str::random(40);
            $seller = new Seller();
            $seller->f_name = $request->f_name;
            $seller->l_name = $request->l_name;
            $seller ->auth_token = $auth_token;
          //  $seller->phone = $request->phone;
           // $seller ->category_id = $request->category_id;
                         
            $seller->email = $request->email;
            $seller->image = ImageManager::upload('seller/', 'png', $request->file('image'));
            $seller->password = bcrypt($request->password);
            $seller->status =  $request->status == 'approved'?'approved': "pending";
            $seller->save();

            // return response()->json(['Shop apply successfully!'], 200);
            // $token = $seller->createToken('LaravelAuthApp')->accessToken;
            // return response()->json(['token' => $token], 200);
            $seller_id = Seller::latest()->first()->id;
            $shop = new Shop();
            $shop->seller_id = $seller_id;
            $shop->subscription = $request->subscription;
            $shop->name = $request->shop_name;
            $shop->whats_up = $request->whats_up;
            $shop->address = $request->shop_address;
            $shop->contact = $request->phone;
            $shop->latitude = $request->latitude;
            $shop->longitude = $request->longitude;
            $shop->zone_id = $request->zone_id;
            $shop->image = ImageManager::upload('shop/', 'png', $request->file('logo'));
            $shop->banner = ImageManager::upload('shop/banner/', 'png', $request->file('banner'));
            $shop->save();

            DB::table('seller_wallets')->insert([
                'seller_id' => $seller['id'],
                'withdrawn' => 0,
                'commission_given' => 0,
                'total_earning' => 0,
                'pending_withdraw' => 0,
                'delivery_charge_earned' => 0,
                'collected_cash' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            // $token = $seller->createToken('LaravelAuthApp')->accessToken;
            // return response()->json(['token' => $token], 200); 
         });

        if($request->status == 'approved'){
            return response()->json(['Shop apply successfully!'], 200);
        }else{
            return response()->json(['Shop apply successfully!'], 200);
            return redirect()->route('seller.auth.login');
        }
    
  }
  public function subscription(){
    return $subscriptions = response()->json(SubscriptionSeller::all(), 200);
  }
}
