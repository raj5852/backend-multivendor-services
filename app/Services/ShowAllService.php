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
        return VendorService::query()
            ->where('status', 'active')
            ->latest()
            ->with(['user:id,name,image','firstpackage:id,price,vendor_service_id'])
            ->paginate(12);
    }
}
