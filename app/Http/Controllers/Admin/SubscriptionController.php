<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\SubscriptionSeller;
use App\Model\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $categories = SubscriptionSeller::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }
        // else{
        //     $categories = Subscription::where(['position' => 0]);
        // }

        $categories = SubscriptionSeller::get();
        return view('admin-views.subscriptions.view', compact('categories','search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'value' => 'required',
            'time' => 'required',
        ], [
            'name.required' => 'Subscription name is required!',
            'value.required' => 'Subscription value is required!',
            'time.required' => 'Subscription Time is required!',
        ]);

        $category = new SubscriptionSeller;
        $category->name = $request->name;
        $category->value = $request->value;
        $category->time = $request->time;
        $category->save();

        Toastr::success('Subscription added successfully!');
        return back();
    }

    public function edit(Request $request, $id)
    {
        $category = SubscriptionSeller::withoutGlobalScopes()->find($id);
        return view('admin-views.subscriptions.category-edit', compact('category'));
    }

    public function update(Request $request)
    {
        $category = SubscriptionSeller::find($request->id);
        $category->name = $request->name;
        $category->value = $request->value;
        $category->time = $request->time;
        $category->save();

        Toastr::success('Subscription updated successfully!');
        return back();
    }

    public function delete(Request $request,  $id)
    {
        //    return $id;
       // $categories = SubscriptionSeller::where('id', $request->id)->get();
        SubscriptionSeller::destroy($id);

        return back();
    }
    public function status(Request $request)
    {
        $category = SubscriptionSeller::find($request->id);
        $category->home_status = $request->home_status;
        $category->save();
        // Toastr::success('Service status updated!');
        // return back();
        return response()->json([
            'success' => 1,
        ], 200);
    }
}
