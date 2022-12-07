<?php

namespace App\Http\Controllers\delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\DeliveryHistory;
Use Auth;
class deliveryController extends Controller
{

    public function logindel(){
        return view('delivery-views.login');
           }
   
       public function login(Request $request){
        // return $request;
            if (Auth::guard('employ')->attempt(['email' => request('email'), 'password' => request('password')])){

                return redirect()->route('admin-delivery.dashboard');
            }else{
                  return redirect()->back()->withErrors(['Credentials does not match.']);
            }
       }

     public function dashboard(){

        $deliveryhistory = DeliveryHistory::all();
        return view('delivery-views.system.dashboard',compact('deliveryhistory'));
    }

           public function logout(Request $request)
           {
               auth()->guard('employ')->logout();
       
               $request->session()->invalidate();
       
               return redirect()->route('admin-delivery.auth.login');
           }
}
