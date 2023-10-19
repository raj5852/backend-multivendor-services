<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\Cart;
use App\Models\Order;
use App\Models\PendingBalance;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Class ProductCheckoutService.
 */
class ProductCheckoutService
{

    static   function store($cartId, $productid, $totalqty, $userid, $datas)
    {
        $cart = Cart::find($cartId);
        if (!$cart) {
            return false;
        }
        $product = Product::find($productid);

        $categoryId = $cart->category_id;

        foreach ($datas as $data) {

            if ($cart->purchase_type == 'single' || $product->is_connect_bulk_single == 1) {
                $product->decrement('qty', $totalqty);

                $result = [];
                $databaseValue = $product;

                if ($databaseValue->variants != '') {

                    foreach ($databaseValue->variants as $dbItem) {
                        foreach ($data['variants'] as $userItem) {
                            if ($dbItem['id'] == $userItem['variant_id']) {
                                $dbItem['qty'] -= $userItem['qty'];
                                break;
                            }
                        }
                        $result[] = $dbItem;
                    }

                    $databaseValue->variants = $result;
                    $databaseValue->save();
                }
            }



            $vendor_balance = User::find($product->user_id);


            $afi_amount = $totalqty * $cart->amount;

            if ($vendor_balance->balance > $afi_amount) {
                $status = Status::Pending->value;
                $vendor_balance->balance = ($vendor_balance->balance - $afi_amount);
                $vendor_balance->save();
            } else {
                $status = Status::Hold->value;
            }

            $order =   Order::create([
                'vendor_id' => $product->user_id,
                'affiliator_id' => $userid,
                'product_id' => $product->id,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'city' => $data['city'],
                'address' => $data['address'],
                'variants' => json_encode($data['variants']),
                'afi_amount' => $afi_amount,
                'product_amount' => $cart->product_price * $totalqty,
                'status' =>  $status,
                'category_id' => $categoryId,
                'qty' => $totalqty,
                'totaladvancepayment' => $cart->advancepayment * $totalqty
            ]);


            PendingBalance::create([
                'affiliator_id' => $userid,
                'product_id' => $product->id,
                'order_id' => $order->id,
                'qty' => $totalqty,
                'amount' => $afi_amount,
                'status' => Status::Pending->value
            ]);
        }

        DB::table('carts')->where('id', $cartId)->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Checkout successfully!'
        ]);
    }
}
