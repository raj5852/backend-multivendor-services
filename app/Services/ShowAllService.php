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
            ->with(['user:id,name,image', 'firstpackage:id,price,vendor_service_id'])
            ->select('id', 'title', 'user_id', 'image', 'tags')
            ->withAvg('servicerating', 'rating')
            ->when(request('tags') != '', function ($query) {
                $query->whereJsonContains('tags', request('tags')); // Use 'tags' here
            })
            ->inRandomOrder()
            ->paginate(12);

        $datas->each(function ($item) {
            $item->servicerating_avg_rating = number_format($item->servicerating_avg_rating ?? 0, 2);
        });

        return $datas;
    }
}
