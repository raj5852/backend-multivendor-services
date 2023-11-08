<?php

namespace App\Services;

use App\Models\VendorService;

/**
 * Class ShowAllService.
 */
class ShowAllService
{

    static function show()
    {
        $datas = VendorService::query()
            ->where('status', 'active')
            ->latest()
            ->with(['user:id,name,image', 'firstpackage:id,price,vendor_service_id'])
            ->select('id', 'title', 'user_id', 'image')
            ->withAvg('servicerating', 'rating')
            ->paginate(12);


        $datas->each(function ($item) {
            $item->servicerating_avg_rating = number_format($item->servicerating_avg_rating ?? 0, 2);
        });
        return $datas;
    }
}
