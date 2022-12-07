<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Model\Seller;
use Brian2694\Toastr\Facades\Toastr;
use Spatie\Permission\Models\Role;
use DB;
use Auth;
use Hash;

class EmployeController extends Controller
{
   /**
* Display a listing of the resource.
*
* @return \Illuminate\Http\Response
*/
public function index(Request $request)
{
 $user = Auth('seller')->user()->id;
$data = Seller::where('added' , $user)->orderBy('id','DESC')->paginate(5);
return view('seller-views.users.show_users',compact('data'))
->with('i', ($request->input('page', 1) - 1) * 5);
}


/**
* Show the form for creating a new resource.
*
* @return \Illuminate\Http\Response
*/
public function create()
{
    $roles = Role::pluck('name','name')->all();
return view('seller-views.users.Add_user',compact('roles'));

}
/**
* Store a newly created resource in storage.
*
* @param  \Illuminate\Http\Request  $request
* @return \Illuminate\Http\Response
*/
public function store(Request $request)
{
  // return $request;

$this->validate($request, [
'f_name' => 'required',
'l_name' => 'required',
'email' => 'required|email|unique:sellers,email',
'password' => 'required|same:confirm-password',
'roles_name' => 'required'
]);
  //$input['password'] = Hash::make($input['password']);
 // return auth('seller')->user()->roles_name;
$user =new Seller();
$user->f_name = $request->f_name ;
$user->l_name = $request->l_name ;
$user->email = $request->email ;
$user->status = 'approved';
$user->password = bcrypt($request->password);
//if(auth('seller')->user()->roles_name == 'seller'){
$user->added = auth('seller')->user()->id;
 //}
 //$user->added = auth('seller')->user()->id;
$user-> save();
$user->assignRole($request->roles_name);
Toastr::success('Employee added successfully!');
return redirect()->route('seller.users.index');
}

/**
* Display the specified resource.
*
* @param  int  $id
* @return \Illuminate\Http\Response
*/
public function show($id)
{
$user = Seller::find($id);
return view('seller-views.users.show',compact('user'));
}
/**
* Show the form for editing the specified resource.
*
* @param  int  $id
* @return \Illuminate\Http\Response
*/
public function edit($id)
{
$user = Seller::find($id);
$roles = Role::pluck('name','name')->all();
$userRole = $user->roles->pluck('name','name')->all();
return view('seller-views.users.edit',compact('user','roles','userRole'));
}
/**
* Update the specified resource in storage.
*
* @param  \Illuminate\Http\Request  $request
* @param  int  $id
* @return \Illuminate\Http\Response
*/
public function update(Request $request, $id)
{
$this->validate($request, [
'f_name' => 'required',
'l_name' => 'required',
'email' => 'required|email|unique:employs,email,'.$id,
'password' => 'same:confirm-password',
'roles' => 'required'
]);
$input = $request->all();
if(!empty($input['password'])){
$input['password'] = Hash::make($input['password']);
}else{
$input = array_except($input,array('password'));
}
$user = Seller::find($id);
$user->update($input);
DB::table('model_has_roles')->where('model_id',$id)->delete();
$user->assignRole($request->input('roles'));
Toastr::success('Employee Updated successfully!');
return redirect()->route('seller.users.index');
}
/**
* Remove the specified resource from storage.
*
* @param  int  $id
* @return \Illuminate\Http\Response
*/
public function destroy(Request $request)
{
    Seller::find($request->user_id)->delete();
    Toastr::success('Employee Deleted successfully!');
    return redirect()->route('sellers.users.index');
}
}