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
        $datas =  VendorService::query()
            ->where('status', 'active')
            ->latest()
            ->withAvg('servicerating','rating')
            ->with(['user:id,name,image','firstpackage:id,price,vendor_service_id'])
            ->select('id','title','user_id','image')
            ->paginate(12);
        foreach($datas as $data){
            if($data->servicerating_avg_rating == null){
                $data->servicerating_avg_rating = '0.00';
            }
        }
        return $datas;
    }
}
