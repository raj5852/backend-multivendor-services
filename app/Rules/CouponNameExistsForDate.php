<?php

namespace App\Rules;

use App\Models\Coupon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CouponNameExistsForDate implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

    }
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Coupon name is not valid.';
    }
}
