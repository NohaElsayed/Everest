<?php

namespace App\Http\Controllers\Seller\Auth;

use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Seller;
use App\Model\Shop;
use App\Model\Zone;
use App\CentralLogics\CentraLs;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use App\Model\Category;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\SubscriptionSeller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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

    public function create()
    {
       
        $cat = Category::where(['parent_id' => 0])->get();
        $subscriptions = SubscriptionSeller::get();
        return view('seller-views.auth.register', compact('cat','subscriptions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
        
            'latitude' => 'required',
            'longitude' => 'required',
            // 'subscription' => 'required',
            'zone_id' => 'required',
        ]);

        $this->validate($request, [
            'email' => 'required|unique:sellers',
            'shop_address' => 'required',
            // 'category_id' => 'required',
            'f_name' => 'required',
            'l_name' => 'required',
            'phone' => 'required',
            'subscription' => 'required',
            'password' => 'required|min:8',
        ]);

        if($request->zone_id)
        {
            $point = new Point($request->latitude, $request->longitude);
            $zone = Zone::contains('coordinates', $point)->where('id', $request->zone_id)->first();
            if(!$zone){
                $validator->getMessageBag()->add('latitude', trans('messages.coordinates_out_of_zone'));
                return back()->withErrors($validator)
                        ->withInput();
            }
        }
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        DB::transaction(function ($r) use ($request) {
            $seller = new Seller();
            $seller->f_name = $request->f_name;
            $seller->l_name = $request->l_name;
          //  $seller->phone = $request->phone;
            $seller ->category_id = $request->category_id;
            $seller->assignRole('seller');
            $seller->email = $request->email;
            $seller->image = ImageManager::upload('seller/', 'png', $request->file('image'));
            $seller->password = bcrypt($request->password);
            $seller->status =  $request->status == 'approved'?'approved': "pending";
            $seller->save();

            $shop = new Shop();
            $shop->seller_id = $seller->id;
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

        });

        if($request->status == 'approved'){
            Toastr::success('Shop apply successfully!');
            return back();
        }else{
            Toastr::success('Shop apply successfully!');
            return redirect()->route('seller.auth.login');
        }
        

        
    }
}
