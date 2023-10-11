<?php

namespace App\Http\Requests;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // 'purchase_type'=>['required',Rule::in(['single','bulk'])] ,
            // 'product_id' => ['required'],
            'payment_type'=>['required',Rule::in(['aamarpay','my-wallet'])],
            'cart_id'=>['required',function($attribute,$value,$fail){

                $cart = Cart::where(['user_id'=>userid(),'id'=>request('cart_id')])->first();
                if(!$cart){
                    return $fail('Invalid cart');
                }

            }],
            'datas' => ['required', 'array'],
            'datas.*.name' => ['required'],
            'datas.*.phone' => ['required', 'integer'],
            'datas.*.email' => ['required', 'email'],
            'datas.*.city' => ['required'],
            'datas.*.address' => ['required'],
            'datas.*.variants' => ['required', 'array'],
            'datas.*.variants.*.qty' => ['required', 'integer','min:1']
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }
}
