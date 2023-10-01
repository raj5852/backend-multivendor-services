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
            ->with(['user:id,name,image'])
            ->paginate(12);
    }
}
