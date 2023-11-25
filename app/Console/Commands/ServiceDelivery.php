<?php

namespace App\Console\Commands;

use App\Models\ServiceOrder;
use App\Models\User;
use App\Services\PaymentHistoryService;
use Illuminate\Console\Command;

class ServiceDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service:delivery';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ServiceOrder::where('status', 'delivered')
            ->where('updated_at', '>', now()->addHour(48))
            ->chunk(100, function ($order) {
                $order->update([
                    'success' => 'success'
                ]);

                User::find($order->vendor_id)->increment(['balance' => $order->amount]);
                PaymentHistoryService::store(uniqid(), $order->amount, 'My wallet', 'Service sell', '+', '', $order->vendor_id);
            });
    }
}
