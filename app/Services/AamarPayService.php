<?php

namespace App\Services;

/**
 * Class AamarPayService.
 */
class AamarPayService
{
    static function  gateway($price, $traxId, $type,$successUrl)
    {
        $success = $successUrl;
        $cancel = url('api/aaparpay/cancel');
        $fail = url('api/aaparpay/fail');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => config('app.aamarpay'),
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
                'desc' => 'desc',
                'success_url' => $success,
                'fail_url' => $fail,
                'cancel_url' => $cancel,
                'type' => 'json',
                'opt_a'=>$type
            ],
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return   json_decode($response);
    }
}
