<?php

namespace App\Http\Requests;

use App\Rules\RenewPackageId;
use App\Rules\RenewPaymentRule;
use App\Rules\SubscriptionTypeRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RenewRequest extends FormRequest
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
            'renew_time'=>['required',Rule::in(['monthly','half_yearly','yearly'])],
            // 'subscription_type'=>['required',Rule::in(['vendor','affiliate']), new SubscriptionTypeRule() ],
            'package_id'=>['required', new RenewPackageId()],
            'payment_method'=>['required',Rule::in('my-wallet','aamarpay'),new RenewPaymentRule()]
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
