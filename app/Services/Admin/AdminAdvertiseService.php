<?php

namespace App\Services\Admin;

use App\Enums\Status;
use App\Models\AdminAdvertise;
use App\Models\AdvertiseAudienceFile;
use App\Models\AdvertisePlacement;
use App\Models\DollerRate;
use App\Models\LocationFile;
use App\Models\User;
use App\Services\AamarPayService;
use App\Services\PaymentHistoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use function PHPUnit\Framework\fileExists;

/**
 * Class AdminAdvertiseService.
 */
class AdminAdvertiseService
{
    public static function create($validatData)
    {
        // return $validatData;
        $trxid = uniqid();
        $adminadvaertise = new AdminAdvertise();
        $adminadvaertise->trxid = $trxid;
        $adminadvaertise->campaign_objective   =  $validatData['campaign_objective'];
        $adminadvaertise->user_id  =  userid();
        $adminadvaertise->campaign_name   =  $validatData['campaign_name'];
        $adminadvaertise->conversion_location   =  $validatData['conversion_location'];
        $adminadvaertise->performance_goal   =  $validatData['performance_goal'];
        $adminadvaertise->budget_amount   =  $validatData['budget_amount'];
        $adminadvaertise->start_date   =  $validatData['start_date'];
        $adminadvaertise->end_date   =  $validatData['end_date'];
        $adminadvaertise->age   =  $validatData['age'];
        $adminadvaertise->gender   =  $validatData['gender'];
        $adminadvaertise->detail_targeting   =  $validatData['detail_targeting'];
        $adminadvaertise->country   =  $validatData['country'];
        $adminadvaertise->city   =  $validatData['city'];
        $adminadvaertise->device   =  $validatData['device'];
        $adminadvaertise->platform   =  $validatData['platform'];
        $adminadvaertise->inventory   =  $validatData['inventory'];
        $adminadvaertise->format   =  $validatData['format'];
        $adminadvaertise->ad_creative   =  $validatData['ad_creative'];
        $adminadvaertise->budget   =  $validatData['budget'];
        $adminadvaertise->placements   =  $validatData['placements'];


        $adminadvaertise->destination   =  $validatData['destination'];
        $adminadvaertise->tracking   =  $validatData['tracking'];
        $adminadvaertise->url_perimeter   =  $validatData['url_perimeter'];
        $adminadvaertise->number   =  $validatData['number'];
        $adminadvaertise->last_description   =  $validatData['last_description'];
        $adminadvaertise->status   =  Status::Pending->value;

        $adminadvaertise->save();


        if (request()->hasFile('advertise_audience_files')) {
            foreach (request('advertise_audience_files') as $file) {
                $data = uploadany_file($file, 'uploads/advertise_audience_files/');
                $AdvertiseAudienceFile =  new AdvertiseAudienceFile();
                $AdvertiseAudienceFile->advertise_id  = $adminadvaertise->id;
                $AdvertiseAudienceFile->file    =  $data;
                $AdvertiseAudienceFile->save();
            }
        }


        if (request()->hasFile('location_files')) {
            foreach (request('location_files') as $file) {
                $data = uploadany_file($file, 'uploads/location_files/');
                $locatFile =  new LocationFile();
                $locatFile->advertise_id  = $adminadvaertise->id;
                $locatFile->file    =  $data;
                $locatFile->save();
            }
        }



        $dollerRate  =  DollerRate::first()?->amount;
        $totalprice = ($validatData['budget_amount'] * $dollerRate);
        if (request('paymethod') == 'my-wallet') {
            $user = User::find(userid());
            $user->decrement('balance', $totalprice);
            $adminadvaertise->is_paid = 1;
            $adminadvaertise->save();
            PaymentHistoryService::store($trxid, $totalprice, 'My wallet', 'Advertise', '-', '', userid());
            return responsejson('Successfull!');
        } else {

            $successurl =  url('api/aaparpay/advertise-success');
            return AamarPayService::gateway($totalprice, $trxid, 'advertise', $successurl);
        }
    }
}
