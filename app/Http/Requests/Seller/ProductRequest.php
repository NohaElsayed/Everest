<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'              => 'required',
            'category_id'       => 'required',
            // 'brand_id'          => 'required',
            'unit'              => 'required',
            'images'            => 'required',
            'image'             => 'required',
            'tax'               => 'required|min:0',
            'unit_price'        => 'required|numeric|min:1',
            'purchase_price'    => 'required|numeric|min:1',
            'discount'          => 'required|gt:-1',
            'shipping_cost'     => 'required|gt:-1',
            'code'              => 'required|numeric|min:1|digits_between:6,20|unique:products',
            'minimum_order_qty' => 'required|numeric|min:1',
        ];
    }
    public function messages()
    {
        return [
            'name.required'         => 'Product name is required!',
            'category_id.required'  => 'category  is required!',
            'images.required'       => 'Product images is required!',
            'image.required'        => 'Product thumbnail is required!',
            // 'brand_id.required'     => 'brand  is required!',
            'unit.required'         => 'Unit  is required!',
            'code.min'              => 'The code must be positive!',
            'code.digits_between'   => 'The code must be minimum 6 digits!',
            'minimum_order_qty.required' => 'The minimum order quantity is required!',
            'minimum_order_qty.min' => 'The minimum order quantity must be positive!',
        ];
    }
}
