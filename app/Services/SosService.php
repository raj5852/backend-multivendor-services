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

    static function aamarpay($price, $info)
    {

        $traxId = uniqid();
        $success = url('api/aaparpay/success');
        $cancel = url('api/aaparpay/cancel');
        $fail = url('api/aaparpay/fail');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://sandbox.aamarpay.com/index.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => $price,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'store_id' => 'aamarpaytest',
                'signature_key' => 'dbb74894e82415a2f7ff0ec3a97e4183',
                'cus_name' => 'Customer Name',
                'cus_email' => 'example@gmail.com',
                'cus_phone' => '01870******',
                'amount' => $price,
                'currency' => 'BDT',
                'tran_id' => $traxId,
                'desc' => 'test transaction',
                'success_url' => $success,
                'fail_url' => $fail,
                'cancel_url' => $cancel,
                'type' => 'json'
            ],
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $result = json_decode($response);

        $uniqueId = uniqid();

        if (request()->has('files')) {
            foreach (request('files') as $file) {
                $customerRequrement = new CustomerRequiremnt();
                $customerRequrement->uniquid = $uniqueId;
                $customerRequrement->user_id = userid();
                $customerRequrement->file = fileUpload($file, 'uploads/requirement');
                $customerRequrement->save();
            }
        }

        $info['customer_requirement_id'] =  $uniqueId;

        PaymentStore::create([
            'payment_gateway' => 'aamarpay',
            'trxid' => $traxId,
            'payment_type' => 'vendor_service',
            'info' => $info,
            'customer_requirement_id' => $uniqueId,
        ]);



        return response()->json($result);
    }
}
