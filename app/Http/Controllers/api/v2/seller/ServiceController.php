<?php

namespace App\Http\Controllers\api\v2\seller;

use App\CPU\Convert;
use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Seller;
use App\Model\Translation;
use App\Service;
use App\ServiceOrder;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function App\CPU\translate;

class ServiceController extends Controller
{
    public function list(Request $request)
    {
//        return $request;
        $data = Helpers::get_seller_by_token($request);

        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }
        return response()->json(Service::where(['added_by' => 'seller', 'id' => $seller['id']])->orderBy('id', 'DESC')->get(), 200);
    }

    public function service_order_seller(Request $request){
        $data = Helpers::get_seller_by_token($request);
        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }
        $services = Service::where(['added_by' => 'seller', 'id' => $seller['id']])->orderBy('id', 'DESC')->get();
       return response()->json($screensall = DB::table('service_orders')->whereIn('service_id',$services)->join('services', 'services.id', '=', 'service_orders.service_id')->get(), 200);
        
    }

    public function upload_images(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required',
            'type' => 'required|in:service,thumbnail,meta',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $path = $request['type'] == 'product' ? '' : $request['type'] . '/';
        $image = ImageManager::upload('service/' . $path, 'png', $request->file('image'));

        return response()->json(['image_name' => $image, 'type' => $request['type']], 200);
    }

    public function add_new(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);
        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name'              => 'required',
            'category_id'       => 'required',
            'images'            => 'required',
            'thumbnail'         => 'required',
            'lang'              => 'required',
            'price'        => 'required|min:1',
            'code'              => 'required|unique:products',
        ], [
            'name.required'         => translate('Product name is required!'),
            'category_id.required'  => translate('category  is required!'),
            'images.required'       => translate('Product images is required!'),
            'image.required'        => translate('Product thumbnail is required!'),
            'code.required'         => translate('Code is required!'),
        ]);

        $product = new Service();
       $product->user_id = $seller->id;
        $product->added_by = "seller";

        $product->name = $request->name[array_search(Helpers::default_lang(), $request->lang)];
        $product->slug = Str::slug($request->name[array_search(Helpers::default_lang(), $request->lang)], '-') . '-' . Str::random(6);

        $category = [];

        if ($request->category_id != null) {
            array_push($category, [
                'id' => $request->category_id,
                'position' => 1,
            ]);
        }
        $product->category_ids = json_encode($category);
        $product->code = $request->code;
        $product->details = $request->description[array_search(Helpers::default_lang(), $request->lang)];

        $product->images = json_encode($request->images);
        $product->thumbnail = $request->thumbnail;
        $product->price = $request->price;
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;
        $product->meta_image = $request->meta_image;
        $product->status = 0;
        $product->save();
        $data = [];
        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != Helpers::default_lang()) {
                array_push($data, array(
                    'translationable_type' => 'App\Model\Service',
                    'translationable_id' => $product->id,
                    'locale' => $key,
                    'key' => 'name',
                    'value' => $request->name[$index],
                ));
            }
            if ($request->description[$index] && $key != Helpers::default_lang()) {
                array_push($data, array(
                    'translationable_type' => 'App\Model\Service',
                    'translationable_id' => $product->id,
                    'locale' => $key,
                    'key' => 'description',
                    'value' => $request->description[$index],
                ));
            }
        }
        Translation::insert($data);

        return response()->json(['message' => translate('successfully Service added!')], 200);
    }

    public function edit(Request $request, $id)
    {
        $data = Helpers::get_seller_by_token($request);
        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        $product = Service::withoutGlobalScopes()->with('translations')->find($id);
//        $product = Helpers::product_data_formatting($product);

        return response()->json($product, 200);
    }

    public function update(Request $request, $id)
    {
        $data = Helpers::get_seller_by_token($request);
        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        $product = Service::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required',
            'lang' => 'required',
            'code' => 'required|numeric|min:1|digits_between:6,20|unique:services,code,'.$product->id,
        ], [
            'name.required' => 'Product name is required!',
            'category_id.required' => 'category  is required!',
            'code.min' => 'The code must be positive!',
            'code.digits_between' => 'The code must be minimum 6 digits!',
            'code.required' => 'Code  is required!',
        ]);
        $product->user_id = $seller->id;
        $product->added_by = "seller";

        $product->name = $request->name[array_search(Helpers::default_lang(), $request->lang)];
        $product->slug = Str::slug($request->name[array_search(Helpers::default_lang(), $request->lang)], '-') . '-' . Str::random(6);

        $category = [];
        if ($request->category_id != null) {
            array_push($category, [
                'id' => $request->category_id,
                'position' => 1,
            ]);
        }
        $product->category_ids = json_encode($category);
        $product->code = $request->code;
        $product->details = $request->description[array_search(Helpers::default_lang(), $request->lang)];

        $product->images = json_encode($request->images);
        $product->thumbnail = $request->thumbnail;
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;
        if ($request->has('meta_image')) {
            $product->meta_image = $request->meta_image;
        }
        if ($product->request_status == 2) {
            $product->request_status = 0;
        }
        $product->save();
        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != 'en') {
                Translation::updateOrInsert(
                    [
                        'translationable_type' => 'App\Model\Service',
                        'translationable_id' => $product->id,
                        'locale' => $key,
                        'key' => 'name'
                    ],
                    ['value' => $request->name[$index]]
                );
            }
            if ($request->description[$index] && $key != 'en') {
                Translation::updateOrInsert(
                    [
                        'translationable_type' => 'App\Model\Service',
                        'translationable_id' => $product->id,
                        'locale' => $key,
                        'key' => 'description'
                    ],
                    ['value' => $request->description[$index]]
                );
            }
        }

        return response()->json(['message' => translate('successfully Service updated!')], 200);
    }
    public function status_update(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);
        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        $product = Service::find($request->id);
        $product->status = $request->status;
        $product->save();

        return response()->json([
            'success' => translate('updated successfully'),
        ], 200);
    }

    public function delete(Request $request, $id)
    {
        $data = Helpers::get_seller_by_token($request);
        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        $product = Service::find($id);
//        foreach (json_decode($product['images'], true) as $image) {
//            ImageManager::delete('/service/' . $image);
//        }
        ImageManager::delete('/service/thumbnail/' . $product['thumbnail']);
        $product->delete();
        return response()->json(['message' => translate('successfully Service deleted!')], 200);
    }

//    public function service_order_seller(Request $request ,$id){
//        $data = Helpers::get_seller_by_token($request);
//        if ($data['success'] == 1) {
//            $seller = $data['data'];
//        } else {
//            return response()->json([
//                'auth-001' => translate('Your existing session token does not authorize you any more')
//            ], 401);
//        }
//        $products = Service::where('id', $id);
//        return $products;
//    }
}


