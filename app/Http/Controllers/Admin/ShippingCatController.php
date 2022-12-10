<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\ShippingCat;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShippingCatController extends Controller
{
    //
    public function index_admin()
    {
        $shipping_cats = ShippingCat::get();

        return view('admin-views.shipping-cat.add-new', compact('shipping_cats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|max:200',
            'type' => 'required',
            'amount'     => 'numeric',
        ]);

        DB::table('shipping_cats')->insert([
            'type'          => $request['type'],
            'title'         => $request['title'],
            'amount'        => $request['amount'],
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        Toastr::success('Successfully added.');
        return back();
    }


    public function edit($id)
    {
        if ($id != 1) {
            $method = ShippingCat::where(['id' => $id])->first();
            return view('admin-views.shipping-cat.edit', compact('method'));
        }
        return back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'    => 'required|max:200',
            'type' => 'required',
            'amount'     => 'numeric',
        ]);

        DB::table('shipping_methods')->where(['id' => $id])->update([
            'type'          => $request['type'],
            'title'         => $request['title'],
            'amount'        => $request['amount'],
            'updated_at'    => now(),
        ]);

        Toastr::success('Successfully updated.');
        return redirect()->back();
    }

//    public function setting()
//    {
//        $shipping_methods = ShippingMethod::where(['creator_type' => 'admin'])->get();
//        $all_category_ids = Category::where(['position' => 0])->pluck('id')->toArray();
//        $category_shipping_cost_ids = CategoryShippingCost::where('seller_id',0)->pluck('category_id')->toArray();
//
//        foreach($all_category_ids as $id)
//        {
//            if(!in_array($id,$category_shipping_cost_ids))
//            {
//                $new_category_shipping_cost = new CategoryShippingCost;
//                $new_category_shipping_cost->seller_id = 0;
//                $new_category_shipping_cost->category_id = $id;
//                $new_category_shipping_cost->cost = 0;
//                $new_category_shipping_cost->save();
//            }
//        }
//        $all_category_shipping_cost = CategoryShippingCost::where('seller_id',0)->get();
//        return view('admin-views.shipping-method.setting',compact('all_category_shipping_cost','shipping_methods'));
//    }
    public function delete(Request $request)
    {

        $shipping = ShippingCat::find($request->id);

        $shipping->delete();
        return response()->json();
    }

}
