<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSubscriptionRequest extends FormRequest
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
            'subscription_heading'   => 'required|max:256',
            'subscription_title'   => 'required|max:256',
            // 'subscription_user_type'   => 'required|in:vendor,affiliate',
            // 'subscription_package_type'   => 'required|in:monthly,half_yearly,yearly',
            'card_symbol_icon'   => 'required',
            'subscription_amount'   => 'required|numeric',
            'card_time'   => 'required|max:256',
            'card_heading'   => 'required|max:256',
            'card_feature_title'   => 'required|max:256',
            'card_facilities_title' => 'required',

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
