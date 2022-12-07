<?php

namespace App\Http\Controllers\delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\DeliveryHistory;
use App\CPU\ImageManager;
use Brian2694\Toastr\Facades\Toastr;
Use Auth;
use App\Employ;
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

           public function profile(){

           $data = Auth::guard('employ')->user();
           return view('delivery-views.profile', compact('data'));

     }
     public function update_profile(Request $request, $id)
     {
         $employee = Employ::find(auth('employ')->id());
         $employee->name = $request->name;
         $employee->email = $request->email;
        //  $employee->password = $request->password;
         if ($request->photo) {
            $employee->photo = ImageManager::update('delivery/', $employee->image, 'png', $request->file('photo'));
        }
         $employee->save();

         Toastr::info('Admin Delivery updated successfully!');
         return redirect()->back();
     }

     public function settings_password_update(Request $request)
     {
         $request->validate([
             'password' => 'required|same:confirm_password|min:8',
             'confirm_password' => 'required',
         ]);
 
         $seller = Employ::find(auth('employ')->id());
         $seller->password = bcrypt($request['password']);
         $seller->save();
         Toastr::success('Admin Delivery password updated successfully!');
         return back();
     }
}
