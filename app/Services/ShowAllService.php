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
        return VendorService::with(['serviceorders' => function ($query) {
            // $query->where('');
        }])->get();
    }
}
