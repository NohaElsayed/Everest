<?php

namespace App\Http\Controllers\Admin;

use App\Advert;
use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Translation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class AdvertController extends Controller
{
    public function add_new()
    {
        $br = Advert::latest()->paginate(Helpers::pagination_limit());
        return view('admin-views.adverts.add-new', compact('br'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name.0' => 'required|unique:brands,name',
        ], [
            'name.0.required'   => 'Advert name is required!',
            'name.0.unique'     => 'The Advert has already been taken.',
        ]);

        $brand = new Advert;
        $brand->name = $request->name[array_search('en', $request->lang)];
        $brand->image = ImageManager::upload('adverts/', 'png', $request->file('image'));
        $brand->status = 1;
        $brand->save();

        foreach($request->lang as $index=>$key)
        {
            if($request->name[$index] && $key != 'en')
            {
                Translation::updateOrInsert(
                    ['translationable_type'  => 'App\Model\Advert',
                        'translationable_id'    => $brand->id,
                        'locale'                => $key,
                        'key'                   => 'name'],
                    ['value'                 => $request->name[$index]]
                );
            }
        }
        Toastr::success('Brand added successfully!');
        return back();
    }

    /**
     * Brand list show, search
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    function list(Request $request)
    {
        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';

        $br = Advert::
//        withCount('brandAllProducts')
//            ->with(['brandAllProducts'=> function($query){
//                $query->withCount('order_details');
//            }])
            when($request['search'], function ($q) use($request){
                $key = explode(' ', $request['search']);
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
            })
            ->latest()->paginate(Helpers::pagination_limit())->appends($query_param);

        return view('admin-views.adverts.list', compact('br','search'));
    }

//    public function export(){
//        $brands = Advert::withCount('brandAllProducts')
//            ->with(['brandAllProducts'=> function($query){
//                $query->withCount('order_details');
//            }])->orderBy('id', 'DESC')->get();
//
//        $data = array();
//        foreach($brands as $brand){
//            $data[] = array(
//                'Brand Name'      => $brand->name,
//                'Total Product'   => $brand->brand_all_products_count,
//                'Total Order' => $brand->brandAllProducts->sum('order_details_count'),
//            );
//        }
//
//        return (new FastExcel($data))->download('brand_list.xlsx');
//    }

    public function edit($id)
    {
        $b = Advert::where(['id' => $id])->withoutGlobalScopes()->first();
        return view('admin-views.adverts.edit', compact('b'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name.0' => 'required|unique:brands,name,'.$id,
        ], [
            'name.0.required'   => 'Advert name is required!',
            'name.0.unique'     => 'The Advert has already been taken.',
        ]);

        $brand = Advert::find($id);
        $brand->name = $request->name[array_search('en', $request->lang)];
        if ($request->has('image')) {
            $brand->image = ImageManager::update('adverts/', $brand['image'], 'png', $request->file('image'));
        }
        $brand->save();
        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != 'en') {
                Translation::updateOrInsert(
                    ['translationable_type' => 'App\Model\Advert',
                        'translationable_id' => $brand->id,
                        'locale' => $key,
                        'key' => 'name'],
                    ['value' => $request->name[$index]]
                );
            }
        }

        Toastr::success('Brand updated successfully!');
        return back();
    }

    public function status_update(Request $request)
    {
        $brand = Advert::find($request['id']);
        $brand->status = $request['status'];

        if($brand->save()){
            $success = 1;
        }else{
            $success = 0;
        }
        return response()->json([
            'success' => $success,
        ], 200);
    }

    public function delete(Request $request)
    {
        $translation = Translation::where('translationable_type','App\Model\Advert')
            ->where('translationable_id',$request->id);
        $translation->delete();
        $brand = Advert::find($request->id);
        ImageManager::delete('/adverts/' . $brand['image']);
        $brand->delete();
        return response()->json();
    }
}

