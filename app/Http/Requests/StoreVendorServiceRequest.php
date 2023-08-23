<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreVendorServiceRequest extends FormRequest
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
            'service_category_id' => [
                'required',
                Rule::exists('service_categories', 'id')->where(function ($query) {
                    $query->where(['user_id' => userid()]);
                })
            ],
            'service_sub_category_id' => [
                'required',
                Rule::exists('service_sub_categories','id')->where(function($query){
                    $query->where('user_id',userid());
                })
            ],

            'title'=>'required',
            'description'=>'required',
            'tags'=>'required',


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
