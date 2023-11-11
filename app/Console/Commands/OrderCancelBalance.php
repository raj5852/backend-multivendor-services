<?php

namespace App\Console\Commands;

use App\Models\CancelOrderBalance;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class OrderCancelBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:cancel';

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
        CancelOrderBalance::where('created_at', '>', Carbon::now()->subHours(24))->chunk(100,function ($data) {
            info($data);
        });
    }
}
