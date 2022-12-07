<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Service;
use App\ServiceOrder;
use Illuminate\Http\Request;
use App\Model\Category;
use App\Model\Review;
use App\Model\Product;
use App\CPU\Helpers;
use App\CPU\ImageManager;
use Illuminate\Support\Str;
use App\CPU\BackEndHelper;
use App\Model\FlashDealProduct;
use App\Model\Wishlist;
use App\Model\DealOfTheDay;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use function App\CPU\translate;
use App\Model\Translation;
use App\Model\Cart;

class ServiceController extends Controller
{
    public function add_new()
    {
        $cat = Category::where(['parent_id' => 0])->get();
        return view('admin-views.services.add-new', compact('cat'));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'              => 'required',
            'category_id'       => 'required',
            'images'            => 'required',
            'image'             => 'required',
            'code'              => 'required|numeric|min:1|digits_between:6,20|unique:products',
        ], [
            'images.required'       => 'services images is required!',
            'image.required'        => 'services thumbnail is required!',
            'category_id.required'  => 'category  is required!',
            'code.min'              => 'The code must be positive!',
            'code.digits_between'   => 'The code must be minimum 6 digits!',
        ]);
        if (is_null($request->name[array_search('en', $request->lang)])) {
            $validator->after(function ($validator) {
                $validator->errors()->add(
                    'name', 'Name field is required!'
                );
            });
        }
        $p = new Service();
        $p->user_id = auth('admin')->id();
        $p->added_by = "admin";
        $p->name = $request->name[array_search('en', $request->lang)];
        $p->code = $request->code;
        $p->phone = $request->phone;
        $p->slug = Str::slug($request->name[array_search('en', $request->lang)], '-') . '-' . Str::random(6);

        $category = [];

        if ($request->category_id != null) {
            array_push($category, [
                'id' => $request->category_id,
                'position' => 1,
            ]);
        }
        $p->category_ids = json_encode($category);
        $p->details = $request->description[array_search('en', $request->lang)];
        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        if ($request->ajax()) {
            return response()->json([], 200);
        } else {
            if ($request->file('images')) {
                foreach ($request->file('images') as $img) {
                    $product_images[] = ImageManager::upload('service/', 'png', $img);
                }
                $p->images = json_encode($product_images);
            }
            $p->thumbnail = ImageManager::upload('service/thumbnail/', 'png', $request->image);

            $p->meta_title = $request->meta_title;
            $p->meta_description = $request->meta_description;
            $p->meta_image = ImageManager::upload('service/meta/', 'png', $request->meta_image);
            $p->request_status = 1;
            $p->save();

            $data = [];
            foreach ($request->lang as $index => $key) {
                if ($request->name[$index] && $key != 'en') {
                    array_push($data, array(
                        'translationable_type' => 'App\Model\Service',
                        'translationable_id' => $p->id,
                        'locale' => $key,
                        'key' => 'name',
                        'value' => $request->name[$index],
                    ));
                }
                if ($request->description[$index] && $key != 'en') {
                    array_push($data, array(
                        'translationable_type' => 'App\Model\Service',
                        'translationable_id' => $p->id,
                        'locale' => $key,
                        'key' => 'description',
                        'value' => $request->description[$index],
                    ));
                }
            }
            Translation::insert($data);
            Toastr::success('Service added successfully!');
            return redirect()->route('admin.service.list');
        }
    }
    public function view($id)
    {
        $product = Service::with(['reviews'])->where(['id' => $id])->first();
        $reviews = Review::where(['service_id' => $id])->paginate(Helpers::pagination_limit());
        return view('admin-views.services.view', compact('product', 'reviews'));
    }

    public function orders(Request $request)
    {
        $services= ServiceOrder::all();
      //  $services->paginate(Helpers::pagination_limit());
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $pro = $services->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }
        return view('admin-views.services.service-order', compact('services'));
    }

    public function approve_status(Request $request)
    {
        $product = Service::find($request->id);
        $product->request_status = ($product['request_status'] == 0) ? 1 : 0;
        $product->save();

        return redirect()->route('admin.services.list', ['seller', 'status' => $product['request_status']]);
    }

    function list(Request $request, $type)
    {
        $query_param = [];
        $search = $request['search'];
        if ($type == 'in_house') {
            $pro = Service::active()->where(['added_by' => 'admin']);
        } else {
            $pro = Service::active()->where(['added_by' => 'seller'])->where('request_status', $request->status);
        }

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $pro = $pro->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }

        $request_status = $request['status'];
        $pro = $pro->orderBy('id', 'DESC')->paginate(Helpers::pagination_limit())->appends(['status' => $request['status']])->appends($query_param);
        return view('admin-views.services.list', compact('pro', 'search', 'request_status'));
    }

    public function updated_product_list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $pro = Product::where(['added_by' => 'seller'])
                ->where('is_shipping_cost_updated',0)
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->Where('name', 'like', "%{$value}%");
                    }
                });
            $query_param = ['search' => $request['search']];
        } else {
            $pro = Product::where(['added_by' => 'seller'])->where('is_shipping_cost_updated',0);
        }
        $pro = $pro->orderBy('id', 'DESC')->paginate(Helpers::pagination_limit())->appends($query_param);

        return view('admin-views.services.updated-product-list', compact('pro', 'search'));
    }

    public function status_update(Request $request)
    {

        $product = Service::where(['id' => $request['id']])->first();
        $success = 1;

        if ($request['status'] == 1) {
            if ($product->added_by == 'seller' && ($product->request_status == 0 || $product->request_status == 2)) {
                $success = 0;
            } else {
                $product->status = $request['status'];
            }
        } else {
            $product->status = $request['status'];
        }
        $product->save();
        return response()->json([
            'success' => $success,
        ], 200);
    }

    public function get_categories(Request $request)
    {
        $cat = Category::where(['parent_id' => $request->parent_id])->get();
        $res = '<option value="' . 0 . '" disabled selected>---Select---</option>';
        foreach ($cat as $row) {
            if ($row->id == $request->sub_category) {
                $res .= '<option value="' . $row->id . '" selected >' . $row->name . '</option>';
            } else {
                $res .= '<option value="' . $row->id . '">' . $row->name . '</option>';
            }
        }
        return response()->json([
            'select_tag' => $res,
        ]);
    }



    public function edit($id)
    {
        $product = Service::withoutGlobalScopes()->with('translations')->find($id);
        $product_category = json_decode($product->category_ids);
        $product->colors = json_decode($product->colors);
        $categories = Category::where(['parent_id' => 0])->get();
        // $br = Brand::orderBY('name', 'ASC')->get();

        return view('admin-views.services.edit', compact('categories', 'product', 'product_category'));
    }

    public function update(Request $request, $id)
    {

        $product = Service::find($id);
        $validator = Validator::make($request->all(), [
            'name'              => 'required',
            'category_id'       => 'required',
//            'unit_price'        => 'required|numeric|min:1',
            'code'              => 'required|numeric|min:1|digits_between:6,20|unique:services,code,'.$product->id,
        ], [
            'name.required'         => 'Product name is required!',
            'category_id.required'  => 'category  is required!',
            'code.min'              => 'The code must be positive!',
            'code.digits_between'   => 'The code must be minimum 6 digits!',
        ]);

        if (is_null($request->name[array_search('en', $request->lang)])) {
            $validator->after(function ($validator) {
                $validator->errors()->add(
                    'name', 'Name field is required!'
                );
            });
        }
         if (is_null($request->description[array_search('en', $request->lang)])) {
             $validator->after(function ($validator) {
                 $validator->errors()->add(
                     'description', 'Description field is required!'
                 );
             });
         }
        $product->name = $request->name[array_search('en', $request->lang)];

        $category = [];
        if ($request->category_id != null) {
            array_push($category, [
                'id' => $request->category_id,
                'position' => 1,
            ]);
        }
        $product->category_ids = json_encode($category);
        $product->phone = $request->phone;
        $product->code = $request->code;
        $product->details = $request->description[array_search('en', $request->lang)];
        $product_images = json_decode($product->images);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $product->price = BackEndHelper::currency_to_usd($request->unit_price);
        if ($product->added_by == 'seller' && $product->request_status == 2) {
            $product->request_status = 1;
        }
        if ($request->ajax()) {
            return response()->json([], 200);
        } else {
            if ($request->file('images')) {
                foreach ($request->file('images') as $img) {
                    $product_images[] = ImageManager::upload('service/', 'png', $img);
                }
                $product->images = json_encode($product_images);
            }

            if ($request->file('image')) {
                $product->thumbnail = ImageManager::update('service/thumbnail/', $product->thumbnail, 'png', $request->file('image'));
            }

            $product->meta_title = $request->meta_title;
            $product->meta_description = $request->meta_description;
            if ($request->file('meta_image')) {
                $product->meta_image = ImageManager::update('service/meta/', $product->meta_image, 'png', $request->file('meta_image'));
            }

            $product->save();

            foreach ($request->lang as $index => $key) {
                if ($request->name[$index] && $key != 'en') {
                    Translation::updateOrInsert(
                        ['translationable_type' => 'App\Model\Service',
                            'translationable_id' => $product->id,
                            'locale' => $key,
                            'key' => 'name'],
                        ['value' => $request->name[$index]]
                    );
                }
                if ($request->description[$index] && $key != 'en') {
                    Translation::updateOrInsert(
                        ['translationable_type' => 'App\Model\Service',
                            'translationable_id' => $product->id,
                            'locale' => $key,
                            'key' => 'description'],
                        ['value' => $request->description[$index]]
                    );
                }
            }
            Toastr::success('Service updated successfully.');
            return back();
        }
    }

    public function remove_image(Request $request)
    {
        ImageManager::delete('/service/' . $request['image']);
        $product = Service::find($request['id']);
        $array = [];
        if (count(json_decode($product['images'])) < 2) {
            Toastr::warning('You cannot delete all images!');
            return back();
        }
        foreach (json_decode($product['images']) as $image) {
            if ($image != $request['name']) {
                array_push($array, $image);
            }
        }
        Service::where('id', $request['id'])->update([
            'images' => json_encode($array),
        ]);
        Toastr::success('Service image removed successfully!');
        return back();
    }

    public function delete($id)
    {
        $product = Service::find($id);

        $translation = Translation::where('translationable_type', 'App\Model\Service')
            ->where('translationable_id', $id);
        $translation->delete();

//        Cart::where('product_id', $product->id)->delete();
//        Wishlist::where('product_id', $product->id)->delete();

        foreach (json_decode($product['images'], true) as $image) {
            ImageManager::delete('/service/' . $image);
        }
        ImageManager::delete('/service/thumbnail/' . $product['thumbnail']);
        $product->delete();

//        FlashDealProduct::where(['service_id' => $id])->delete();
//        DealOfTheDay::where(['service_id' => $id])->delete();

        Toastr::success('Service removed successfully!');
        return back();
    }

    // public function bulk_import_index()
    // {
    //     return view('admin-views.product.bulk-import');
    // }
}

