<?php


namespace App\Http\Controllers\api\v1\auth;

use App\Advert;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Zone;
use App\ServiceOrder;
use App\User;
use App\Service;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function App\CPU\translate;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class PassportAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:8',
//            'type' => 'required',
//            'age' => 'required',
//            'latitude' => 'required',
//            'longitude' => 'required',
//            'zone_id' => 'required',
        ], [
            'f_name.required' => 'The first name field is required.',
            'l_name.required' => 'The last name field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $temporary_token = Str::random(40);
//        $user = User::create([
//            'f_name' => $request->f_name,
//            'l_name' => $request->l_name,
//            'email' => $request->email,
//            'phone' => $request->phone,
//            'type' => $request['type'],
//            'age' => $request['age'],
//            'is_active' => 1,
//            'password' => bcrypt($request->password),
//            'temporary_token' => $temporary_token,
//        ]);
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
        $user = new User();
        $user->f_name = $request->f_name;
        $user->l_name = $request->l_name;
        $user->phone = $request->phone;
        $user ->email = $request->email;
        $user ->type = $request->type;
        $user->age = $request->age;
        $user->latitude = $request->latitude;
        $user ->longitude = $request->longitude;
        $user ->zone_id = $request->zone_id;
        $user ->password = bcrypt($request['password']);
        $user ->temporary_token = $temporary_token;
        $user ->is_active = 1;
        $user->save();

        $phone_verification = Helpers::get_business_settings('phone_verification');
        $email_verification = Helpers::get_business_settings('email_verification');
        if ($phone_verification && !$user->is_phone_verified) {
            return response()->json(['temporary_token' => $temporary_token], 200);
        }
        if ($email_verification && !$user->is_email_verified) {
            return response()->json(['temporary_token' => $temporary_token], 200);
        }

        $token = $user->createToken('LaravelAuthApp')->accessToken;
        return response()->json(['token' => $token], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = $request['email'];
        if (filter_var($user_id, FILTER_VALIDATE_EMAIL)) {
            $medium = 'email';
        } else {
            $count = strlen(preg_replace("/[^\d]/", "", $user_id));
            if ($count >= 9 && $count <= 15) {
                $medium = 'phone';
            } else {
                $errors = [];
                array_push($errors, ['code' => 'email', 'message' => 'Invalid email address or phone number']);
                return response()->json([
                    'errors' => $errors
                ], 403);
            }
        }

        $data = [
            $medium => $user_id,
            'password' => $request->password
        ];

        $user = User::where([$medium => $user_id])->first();

        if (isset($user) && $user->is_active && auth()->attempt($data)) {
            $user->temporary_token = Str::random(40);
            $user->save();

            $phone_verification = Helpers::get_business_settings('phone_verification');
            $email_verification = Helpers::get_business_settings('email_verification');
            if ($phone_verification && !$user->is_phone_verified) {
                return response()->json(['temporary_token' => $user->temporary_token], 200);
            }
            if ($email_verification && !$user->is_email_verified) {
                return response()->json(['temporary_token' => $user->temporary_token], 200);
            }

            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
            User::where(['id' => auth()->id()])->update(['temporary_token' => $token]);
            return response()->json(['token' => $token], 200);
        } else {
            $errors = [];
            array_push($errors, ['code' => 'auth-001', 'message' => translate('Customer_not_found_or_Account_has_been_suspended')]);
            return response()->json([
                'errors' => $errors
            ], 401);
        }
    }
    public function zones(){
        return $zones = response()->json(Zone::all(), 200);

    }
    public function adverts(){
        return $adverts = response()->json(Advert::all(), 200);

    }
    public function services(){
        return $services = response()->json(Service::all(), 200);

    }
    public function store(Request $request){
      //  return $request;
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);
        $service = new ServiceOrder();
        $service->service_id =$request->service_id;
        $service->notes =$request->notes;
        $service->phone =$request->phone;
        $service->save();
        return response()->json('Request Service Successfully', 200);
    }
}
