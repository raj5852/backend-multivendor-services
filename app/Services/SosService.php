<?php

namespace App\Services;

use App\Models\SupportBox;

/**
 * Class SosService.
 */
class SosService
{

    static function ticketcreate($data){
        $user = auth()->user();
        $supportBox = new SupportBox();
        $supportBox->user_id = $user->id;
        $supportBox->support_box_category_id = $data['support_box_category_id'];
        $supportBox->support_problem_topic_id = $data['support_problem_topic_id'];
        if($data['file']){
            $supportBox->file = fileUpload($data['file'],'uploads/support');
        }
        $supportBox->description = $data['description'];
        $supportBox->save();
        return true;
    }
}
