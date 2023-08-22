<?php

namespace App\Services\Admin;

use App\Models\AdminAdvertise;

/**
 * Class AdminAdvertiseService.
 */
class AdminAdvertiseService
{
    public static function create($validatData)
    {
        $user_id = request()->user()->id;

        AdminAdvertise::create([
            ''
        ]);
    }
}
