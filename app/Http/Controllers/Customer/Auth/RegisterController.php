<?php

namespace App\Http\Controllers\Customer\Auth;
use App\CPU\CartManager;
use App\CPU\Helpers;
use App\CPU\SMS_module;
use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\Model\PhoneOrEmailVerification;
use App\Model\Wishlist;
use App\User;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use App\Model\Zone;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Session;
use function App\CPU\translate;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:customer', ['except' => ['logout']]);
    }

    public function register()
    {
        session()->put('keep_return_url', url()->previous());
        return view('customer-view.auth.register');
    }

    public function submit(Request $request)
    {
      // return $request;
      $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'unique:users',
            'age' => 'required',
            'type' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'zone_id' => 'required',
            'password' => 'required|min:8|same:con_password'
        ], [
            'f_name.required' => 'First name is required',
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
        $user->save();
        // $user = User::create([
        //     'f_name' => $request['f_name'],
        //     'l_name' => $request['l_name'],
        //     'email' => $request['email'],
        //     'phone' => $request['phone'],
        //     'type' => $request['type'],
        //     'age' => $request['age'],
        //     'is_active' => 1,
        //     'latitude'=> $request['latitude'],
        //     'longitude'=> $request['longitude'],
        //     'zone_id' => $request['zone_id'],
        //     'password' => bcrypt($request['password'])
        // ]);

        $phone_verification = Helpers::get_business_settings('phone_verification');
        $email_verification = Helpers::get_business_settings('email_verification');
        if ($phone_verification && !$user->is_phone_verified) {
            return redirect(route('customer.auth.check', [$user->id]));
        }
        if ($email_verification && !$user->is_email_verified) {
            return redirect(route('customer.auth.check', [$user->id]));
        }

        Toastr::success(translate('registration_success_login_now'));
        return redirect(route('customer.auth.login'));
    }

    public static function check($id)
    {
        $user = User::find($id);

        $token = rand(1000, 9999);
        DB::table('phone_or_email_verifications')->insert([
            'phone_or_email' => $user->email,
            'token' => $token,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $phone_verification = Helpers::get_business_settings('phone_verification');
        $email_verification = Helpers::get_business_settings('email_verification');
        if ($phone_verification && !$user->is_phone_verified) {
            SMS_module::send($user->phone, $token);
            $response = translate('please_check_your_SMS_for_OTP');
            Toastr::success($response);
        }

        if ($email_verification && !$user->is_email_verified) {
            $emailServices_smtp = Helpers::get_business_settings('mail_config');
            if ($emailServices_smtp['status'] == 0) {
                $emailServices_smtp = Helpers::get_business_settings('mail_config_sendgrid');
            }
            if ($emailServices_smtp['status'] == 1) {
                Mail::to($user->email)->send(new \App\Mail\EmailVerification($token));
                $response = translate('check_your_email');
            }else{
                $response= translate('email_failed');
            }

            Toastr::success($response);
        }

        return view('customer-view.auth.verify', compact('user'));
    }

    public static function verify(Request $request)
    {
        Validator::make($request->all(), [
            'token' => 'required',
        ]);

        $email_status = Helpers::get_business_settings('email_verification');
        $phone_status = Helpers::get_business_settings('phone_verification');

        $user = User::find($request->id);
        $verify = PhoneOrEmailVerification::where(['phone_or_email' => $user->email, 'token' => $request['token']])->first();

        if ($email_status == 1 || ($email_status == 0 && $phone_status == 0)) {
            if (isset($verify)) {
                try {
                    $user->is_email_verified = 1;
                    $user->save();
                    $verify->delete();
                } catch (\Exception $exception) {
                    Toastr::info('Try again');
                }

                Toastr::success(translate('verification_done_successfully'));

            } else {
                Toastr::error(translate('Verification_code_or_OTP mismatched'));
                return redirect()->back();
            }

        } else {
            if (isset($verify)) {
                try {
                    $user->is_phone_verified = 1;
                    $user->save();
                    $verify->delete();
                } catch (\Exception $exception) {
                    Toastr::info('Try again');
                }

                Toastr::success('Verification Successfully Done');
            } else {
                Toastr::error('Verification code/ OTP mismatched');
            }

        }

        return redirect(route('customer.auth.login'));
    }

    public static function login_process($user, $email, $password)
    {
        if (auth('customer')->attempt(['email' => $email, 'password' => $password], true)) {
            session()->put('wish_list', Wishlist::where('customer_id', $user->id)->pluck('product_id')->toArray());
            $company_name = BusinessSetting::where('type', 'company_name')->first();
            $message = 'Welcome to ' . $company_name->value . '!';
            CartManager::cart_to_db();
        } else {
            $message = 'Credentials are not matched or your account is not active!';
        }

        return $message;
    }

}
