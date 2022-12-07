<?php

namespace App\Http\Controllers\Seller;

use App\CPU\BackEndHelper;
use App\CPU\Convert;
use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Brand;
use App\Model\Cart;
use App\Model\Category;
use App\Model\Color;
use App\Model\DealOfTheDay;
use App\Model\FlashDealProduct;
use App\Model\Product;
use App\Model\Review;
use App\Model\Translation;
use App\Service;
use App\ServiceOrder;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;
use function App\CPU\translate;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class ServiceController extends Controller
{
    public function add_new()
    {
        $cat = Category::where(['parent_id' => 0])->get();
        $br = Brand::orderBY('name', 'ASC')->get();
        return view('seller-views.service.add-new', compact('cat', 'br'));
    }

    public function status_update(Request $request)
    {
        if ($request['status'] == 0) {
            Service::where(['id' => $request['id'], 'added_by' => 'seller', 'user_id' => \auth('seller')->id()])->update([
                'status' => $request['status'],
            ]);
            return response()->json([
                'success' => 1,
            ], 200);
        } elseif ($request['status'] == 1) {
            if (Service::find($request['id'])->request_status == 1) {
                Service::where(['id' => $request['id']])->update([
                    'status' => $request['status'],
                ]);
                return response()->json([
                    'success' => 1,
                ], 200);
            } else {
                return response()->json([
                    'success' => 0,
                ], 200);
            }
        }
    }

//    public function featured_status(Request $request)
//    {
//        if ($request->ajax()) {
//            $product = Service::find($request->id);
//            $product->featured_status = $request->status;
//            $product->save();
//            $data = $request->status;
//            return response()->json($data);
//        }
//    }

    public function store(Request $request)
    {
//        return $request;
        $validator = Validator::make($request->all(), [
            'name'              => 'required',
            'category_id'       => 'required',
            'unit_price'        => 'required|numeric|min:1',
            'code'              => 'required|numeric|min:1|digits_between:6,20|unique:products',

        ], [
            'name.required'         => 'Service name is required!',
            'category_id.required'  => 'category  is required!',
            'images.required'       => 'Service images is required!',
            'image.required'        => 'Service thumbnail is required!',
            'code.min'              => 'The code must be positive!',
            'code.digits_between'   => 'The code must be minimum 6 digits!',
        ]);

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['unit_price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if (is_null($request->name[array_search('en', $request->lang)])) {
            $validator->after(function ($validator) {
                $validator->errors()->add(
                    'name', 'Name field is required!'
                );
            });
        }

        $product = new Service();
        if(auth('employ')->id()){
            $product->emp_id= auth('employ')->user()->id;
            $seller= auth('employ')->user()->added;
            $product->user_id = $seller;
            }else{
            $product->user_id = auth('seller')->id();
        $product->added_by = "seller";
        $product->name = $request->name[array_search('en', $request->lang)];
        $product->slug = Str::slug($request->name[array_search('en', $request->lang)], '-') . '-' . Str::random(6);

        $category = [];

        if ($request->category_id != null) {
            array_push($category, [
                'id' => $request->category_id,
                'position' => 1,
            ]);
        }
        $product->category_ids = json_encode($category);
        $product->code = $request->code;
        $product->details = $request->description[array_search('en', $request->lang)];
        //Generates the combinations of customer choice options
        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $product->status = 0;
        if ($request->ajax()) {
            return response()->json([], 200);
        } else {
            if ($request->file('images')) {
                foreach ($request->file('images') as $img) {
                    $product_images[] = ImageManager::upload('service/', 'png', $img);
                }
                $product->images = json_encode($product_images);
            }
            $product->thumbnail = ImageManager::upload('service/thumbnail/', 'png', $request->file('image'));

            $product->meta_title = $request->meta_title;
            $product->meta_description = $request->meta_description;
            $product->meta_image = ImageManager::upload('service/meta/', 'png', $request->meta_image);

            $product->save();
            $data = [];
            foreach ($request->lang as $index => $key) {
                if ($request->name[$index] && $key != 'en') {
                    array_push($data, array(
                        'translationable_type' => 'App\Model\Service',
                        'translationable_id' => $product->id,
                        'locale' => $key,
                        'key' => 'name',
                        'value' => $request->name[$index],
                    ));
                }
                if ($request->description[$index] && $key != 'en') {
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
            Toastr::success('Service added successfully!');
            return redirect()->route('seller.service.list');
        }
    }
    }
    function list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            if(auth('seller')->user()->added != null){
                $product= auth('seller')->user()->added;
             $products = Service::where(['added_by' => 'seller', 'user_id' => $product])->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
            });
              }else{
            $products = Service::where(['added_by' => 'seller', 'user_id' => \auth('seller')->id()])
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->Where('name', 'like', "%{$value}%");
                    }
                });
            }
            $query_param = ['search' => $request['search']];
        } else {
            if(auth('seller')->user()->added != null){
                $product= auth('seller')->user()->added;
             $products = Service::where(['added_by' => 'seller', 'user_id' => $product]);
              }else{
                $products = Service::where(['added_by' => 'seller', 'user_id' => \auth('seller')->id()]);
              }
        }
        $products = $products->orderBy('id', 'DESC')->paginate(Helpers::pagination_limit())->appends($query_param);

        return view('seller-views.service.list', compact('products', 'search'));
    }
        public function service_order(){

          $services = Service::where(['added_by' => 'seller', 'user_id' => \auth('seller')->id()])->get('id'); 
            // foreach($services as $service){
            //    // $service = ServiceOrder::whereIn('service_id')->get();
            // }
         $services = DB::table('service_orders')->whereIn('service_id',$services)->join('services', 'services.id', '=', 'service_orders.service_id')->get();
            return view('seller-views.service.service-orders', compact('services'));
     
    
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
//        $product->colors = json_decode($product->colors);
        $categories = Category::where(['parent_id' => 0])->get();
//        $br = Brand::orderBY('name', 'ASC')->get();
        return view('seller-views.service.edit', compact('categories',  'product', 'product_category'));

    }

    public function update(Request $request, $id)
    {
        $product = Service::find($id);
        $validator = Validator::make($request->all(), [
            'name'              => 'required',
            'category_id'       => 'required',
            'unit_price'        => 'required|numeric|min:1',
            'code'              => 'required|numeric|min:1|digits_between:6,20|unique:services,code,'.$product->id,
        ], [
            'name.required'                 => 'Product name is required!',
            'category_id.required'          => 'category  is required!',
            'code.min'                      => 'The code must be positive!',
            'code.digits_between'           => 'The code must be minimum 6 digits!',
        ]);

        if (is_null($request->name[array_search('en', $request->lang)])) {
            $validator->after(function ($validator) {
                $validator->errors()->add(
                    'name', 'Name field is required!'
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
        $product->details = $request->description[array_search('en', $request->lang)];
        $product->code = $request->code;
        if ($request->ajax()) {
            return response()->json([], 200);
        } else {
            $product_images = json_decode($product->images);
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
                $product->meta_image = ImageManager::update('product/meta/', $product->meta_image, 'png', $request->file('meta_image'));
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

    public function view($id)
    {
        $product = Service::with(['reviews'])->where(['id' => $id])->first();
        $reviews = Review::where(['product_id' => $id])->paginate(Helpers::pagination_limit());
        return view('seller-views.service.view', compact('product', 'reviews'));
    }

    public function remove_image(Request $request)
    {
        ImageManager::delete('/service/' . $request['image']);
        $product = Product::find($request['id']);
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
        Product::where('id', $request['id'])->update([
            'images' => json_encode($array),
        ]);
        Toastr::success('Service image removed successfully!');
        return back();
    }

    public function delete($id)
    {
        $product = Service::find($id);
        Cart::where('product_id', $product->id)->delete();
        foreach (json_decode($product['images'], true) as $image) {
            ImageManager::delete('/service/' . $image);
        }
        ImageManager::delete('/service/thumbnail/' . $product['thumbnail']);
        $product->delete();
//        FlashDealProduct::where(['product_id' => $id])->delete();
//        DealOfTheDay::where(['product_id' => $id])->delete();
        Toastr::success('Service removed successfully!');
        return back();
    }

//    public function bulk_import_index()
//    {
//        return view('seller-views.product.bulk-import');
//    }

//    public function bulk_import_data(Request $request)
//    {
//        try {
//            $collections = (new FastExcel)->import($request->file('products_file'));
//        } catch (\Exception $exception) {
//            Toastr::error('You have uploaded a wrong format file, please upload the right file.');
//            return back();
//        }
//        $data = [];
//        $skip = ['youtube_video_url', 'details', 'thumbnail'];
//        foreach ($collections as $collection) {
//            foreach ($collection as $key => $value) {
//                if ($key!="" && $value === "" && !in_array($key, $skip)) {
//                    Toastr::error('Please fill ' . $key . ' fields');
//                    return back();
//                }
//            }
//
//            $thumbnail = explode('/', $collection['thumbnail']);
//
//            array_push($data, [
//                'name' => $collection['name'],
//                'slug' => Str::slug($collection['name'], '-') . '-' . Str::random(6),
//                'category_ids' => json_encode([['id' => (string)$collection['category_id'], 'position' => 1], ['id' => (string)$collection['sub_category_id'], 'position' => 2], ['id' => (string)$collection['sub_sub_category_id'], 'position' => 3]]),
//                'brand_id' => $collection['brand_id'],
//                'unit' => $collection['unit'],
//                'min_qty' => $collection['min_qty'],
//                'refundable' => $collection['refundable'],
//                'unit_price' => $collection['unit_price'],
//                'purchase_price' => $collection['purchase_price'],
//                'tax' => $collection['tax'],
//                'discount' => $collection['discount'],
//                'discount_type' => $collection['discount_type'],
//                'current_stock' => $collection['current_stock'],
//                'details' => $collection['details'],
//                'video_provider' => 'youtube',
//                'video_url' => $collection['youtube_video_url'],
//                'images' => json_encode(['def.png']),
//                'thumbnail' => $thumbnail[1] ?? $thumbnail[0],
//                'status' => 0,
//                'colors' => json_encode([]),
//                'attributes' => json_encode([]),
//                'choice_options' => json_encode([]),
//                'variation' => json_encode([]),
//                'featured_status' => 1,
//                'added_by' => 'seller',
//                'user_id' => auth('seller')->id(),
//            ]);
//        }
//        DB::table('products')->insert($data);
//        Toastr::success(count($data) . ' - Products imported successfully!');
//        return back();
//    }

//    public function bulk_export_data()
//    {
//        $products = Product::where(['added_by' => 'seller', 'user_id' => \auth('seller')->id()])->get();
//        //export from product
//        $storage = [];
//        foreach ($products as $item) {
//            $category_id = 0;
//            $sub_category_id = 0;
//            $sub_sub_category_id = 0;
//            foreach (json_decode($item->category_ids, true) as $category) {
//                if ($category['position'] == 1) {
//                    $category_id = $category['id'];
//                } else if ($category['position'] == 2) {
//                    $sub_category_id = $category['id'];
//                } else if ($category['position'] == 3) {
//                    $sub_sub_category_id = $category['id'];
//                }
//            }
//            $storage[] = [
//                'name' => $item->name,
//                'category_id' => $category_id,
//                'sub_category_id' => $sub_category_id,
//                'sub_sub_category_id' => $sub_sub_category_id,
//                'brand_id' => $item->brand_id,
//                'unit' => $item->unit,
//                'min_qty' => $item->min_qty,
//                'refundable' => $item->refundable,
//                'youtube_video_url' => $item->video_url,
//                'unit_price' => $item->unit_price,
//                'purchase_price' => $item->purchase_price,
//                'tax' => $item->tax,
//                'discount' => $item->discount,
//                'discount_type' => $item->discount_type,
//                'current_stock' => $item->current_stock,
//                'details' => $item->details,
//                'thumbnail' => 'thumbnail/' . $item->thumbnail
//
//            ];
//        }
//        return (new FastExcel($storage))->download('products.xlsx');
//    }

//    public function barcode(Request $request, $id)
//    {
//        if ($request->limit > 270) {
//            Toastr::warning(translate('You can not generate more than 270 barcode'));
//            return back();
//        }
//        $product = Product::findOrFail($id);
//        $limit =  $request->limit ?? 4;
//        return view('seller-views.service.barcode', compact('product', 'limit'));
//    }

}
