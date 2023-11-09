<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ProductOrderRequest extends FormRequest
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
        $order = Order::find($this->route('id'));
        $statusRules = [
            'hold' => ['cancel', 'pending'],
            'pending' => ['cancel', 'received'],
            'received' => ['cancel', 'progress'],
            'progress' => ['return', 'delivered'],
        ];

        $invalidstatus = function ($attribute, $value, $fail) use ($order) {
            if (in_array($order->status, ['return', 'delivered', 'cancel'])) {
                $fail('Not possible to change current status');
            }
        };




        $vendorbalance = function ($attribute, $value, $fail) use ($order) {
            if ($order->status == 'hold') {
                $balance = User::find($order->vendor_id)->balance;
                if ($balance <  $order->afi_amount) {
                    $fail('Balance not available!');
                }
            }
        };


        return [
            'status' => [
                'required',
                Rule::in($statusRules[$order->status] ?? []),
                $invalidstatus,
                $vendorbalance,
            ],
            'reason' => 'required_if:status,cancel,return'
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
