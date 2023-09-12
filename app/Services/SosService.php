<?php

namespace App\Services;

use App\Models\CustomerRequiremnt;
use App\Models\PaymentStore;
use App\Models\SupportBox;

/**
 * Class SosService.
 */
class SosService
{

    static function ticketcreate($data)
    {
        $user = auth()->user();
        $supportBox = new SupportBox();
        $supportBox->user_id = $user->id;
        $supportBox->support_box_category_id = $data['support_box_category_id'];
        $supportBox->support_problem_topic_id = $data['support_problem_topic_id'];
        if (request()->hasFile('file')) {
            $supportBox->file = uploadany_file($data['file'], 'uploads/support/');
        }
        $supportBox->description = $data['description'];
        $supportBox->subject = $data['subject'];
        $supportBox->save();
        return true;
    }


    static function aamarpaysubscription($price, $info,$coupon=null)
    {
        $uniqueId = uniqid();
        $successurl = url('api/aaparpay/subscription-success');
        $result =  AamarPayService::gateway($price,$uniqueId,'subscription',$successurl);
        $info['user_id'] = userid();
        $info['coupon_id'] = $coupon;

        PaymentStore::create([
            'payment_gateway' => 'aamarpay',
            'trxid' => $uniqueId,
            'payment_type' => 'subscription',
            'info' => $info,
            'customer_requirement_id' => $uniqueId,
        ]);

        return response()->json($result);
    }

}
