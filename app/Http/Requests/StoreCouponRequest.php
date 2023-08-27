<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreCouponRequest extends FormRequest
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
            'name' => ['required', 'max:256'],
            'type' => ['required|in:flat,percentage'],
            'amount' => ['required'],
            'commission' => ['required'],
            'expire_date' => ['required'],
            'limitation' => ['required'],
            'user_id' => ['required', 'integer',Rule::exists('users', 'id')->whereIn('role_as', [2, 3])]
        ];
    }

    public function failedValidation(Validator  $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }
}
