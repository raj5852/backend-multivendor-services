<?php

namespace App\Services;

use App\Models\AdvancePayment;
use App\Models\CancelOrderBalance;
use App\Models\Order;
use App\Models\PendingBalance;
use App\Models\Product;
use App\Models\User;

/**
 * Class ProductOrderService.
 */
class ProductOrderService
{
    static function orderStatus($validatedData, $id)
    {
        $order = Order::find($id);

        return  match ($validatedData['status']) {
            'pending' => self::pendingdOrder($order),
            'cancel' => self::canceldOrder($order),
            'progress' => self::progressOrder($order),
            'delivered' => self::deliveredOrder($order),
            'return' => self::returnOrder($order),
            'received'=> self::receivedOrder($order)
        };
    }

    static function canceldOrder($order)
    {

        $order->reason = request('reason');
        $order->update(['status' => 'cancel']);

        // order current status
        if ($order->status != 'hold') {
            self::vendorBalanceBack($order);
        }

        // affiliate balance back
        self::affiliateBalanceback($order);
        self::quantityadded($order);

        return  self::response('Order cancel successfull');
    }


    static function affiliateBalanceback($order)
    {
        $advancepayment = AdvancePayment::where('order_id', $order->id)->first();

        if ($advancepayment) {
            CancelOrderBalance::create([
                'user_id' => $order->affiliator_id,
                'balance' => $advancepayment->amount
            ]);
        }
    }

    static function vendorBalanceBack($order)
    {
        $pendingBalance =  self::orderPendingBalance($order);

        CancelOrderBalance::create([
            'user_id' => $order->vendor_id,
            'balance' => $pendingBalance->amount
        ]);
    }





    static function pendingdOrder($order)
    {
        $vendor = User::find($order->vendor_id);
        $vendor->decrement('balance', $order->afi_amount);
        PaymentHistoryService::store(uniqid(), $order->afi_amount, 'My wallet', 'Affiliate commission', '-', '', $order->vendor_id);

        $order->update(['status' => 'pending']);

        return self::response('Order pending successfull!');
    }

    static function receivedOrder($order)
    {
        $order->update(['status' => 'received']);
        return self::response('Order received successfull!');
    }

    static function progressOrder($order)
    {
        $order->update(['status' => 'progress']);
        return self::response('Order progress successfull!');
    }

    static function returnOrder($order)
    {
        $order->reason = request('reason');
        $order->update(['status' => 'return']);

        // order current status
        if ($order->status != 'hold') {
            self::vendorBalanceBack($order);
        }

        // affiliate balance back
        self::affiliateBalanceback($order);


        return self::response('Order retrun successfull!');
    }


    static   function quantityadded($order)
    {
        $balance = PendingBalance::where('order_id', $order->id)->first();


        if ($order->is_unlimited != 1) {
            $product =  Product::find($order->product_id);
            $product->qty = ($product->qty + $balance->qty);

            $variants = json_decode($order->variants);
            $data = collect($variants)->pluck('qty', 'variant_id');


            $result = [];

            foreach ($data as $variantId => $qty) {
                $result[] = [
                    "variant_id" => $variantId,
                    "qty" => $qty
                ];
            }


            $databaseValues = $product->variants;
            $userValues = $result;



            if ($databaseValues != '') {
                foreach ($databaseValues as &$databaseItem) {
                    $variantId = $databaseItem['id'];
                    $matchingUserValue = collect($userValues)->firstWhere('variant_id', $variantId);

                    if ($matchingUserValue) {
                        $userQty = $matchingUserValue['qty'];
                        $databaseItem['qty'] += $userQty;
                    }
                }

                $product->variants = $databaseValues;
            }

            $product->save();
        }
    }


    static function deliveredOrder($order)
    {

        $affiliateData = self::orderPendingBalance($order);

        $affiliator =  User::find($affiliateData->affiliator_id);
        $affiliator->increment('balance', $affiliateData->amount);

        $affiliateData->update(['status' => 'success']);

        PaymentHistoryService::store(uniqid(), $affiliateData->amount, 'My wallet', 'Product commission', '+', '', $affiliator->id);

        $order->update(['status' => 'delivered']);

        return self::response('Order delivered successfully');
    }


    static function orderPendingBalance($order)
    {
        return PendingBalance::where('order_id', $order->id)->first();
    }


    static function response($message)
    {
        return response()->json([
            'status' => 200,
            'message' => $message
        ]);
    }
}
