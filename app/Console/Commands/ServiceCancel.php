<?php

namespace App\Console\Commands;

use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Console\Command;

class ServiceCancel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service:cancel';

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
            ->where('updated_at', '<', now()->subHour(48))
            ->chunk(100, function ($order) {
                // User::find($order->user_id)->
            });
    }
}
