<?php

namespace App\Services\Admin;

use App\Enums\Status;
use App\Models\AdminAdvertise;
use App\Models\AdvertiseAudienceFile;
use App\Models\AdvertisePlacement;
use App\Models\DollerRate;
use App\Models\LocationFile;
use App\Services\AamarPayService;
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
        $trxid = uniqid();
        $adminadvaertise = new AdminAdvertise();
        $adminadvaertise->trxid = $trxid;
        $adminadvaertise->platforms = $validatData['platforms'];
        $adminadvaertise->campaign_objective   =  $validatData['campaign_objective'];
        $adminadvaertise->user_id  =  userid();
        $adminadvaertise->campaign_name   =  $validatData['campaign_name'];
        $adminadvaertise->conversion_location   =  $validatData['conversion_location'];
        $adminadvaertise->performance_goal   =  $validatData['performance_goal'];
        // $adminadvaertise->platforms   =  $validatData['platforms'];
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

        $advertisePlace = new AdvertisePlacement();
        $advertisePlace->advertise_id  = $adminadvaertise->id;
        $advertisePlace->feeds  = request('feeds') ? request('feeds')  : '';
        $advertisePlace->story_reels  = request('story_reels') ? request('story_reels') : '';
        $advertisePlace->adds_video_and_reels  = request('adds_video_and_reels') ? request('adds_video_and_reels') : '';
        $advertisePlace->search_result  = request('search_result') ? request('search_result') : '';
        $advertisePlace->messages  = request('messages') ? request('messages') : '';
        $advertisePlace->apps_and_sites  = request('apps_and_sites') ? request('apps_and_sites') : '';
        $advertisePlace->save();

        if (request('paymethod') == 'my-wallet') {
            return "Successfull!";
        }else{
            $dollerRate  =  DollerRate::first()?->amount;

            $successurl =  url('api/aaparpay/advertise-success');
            return AamarPayService::gateway( ($validatData['budget_amount'] * $dollerRate), $trxid,'advertise',$successurl);
        }
    }


    // public static function update($validatData, $id)
    // {
    //     $userId = userid();
    //     $adminadvaertise =  AdminAdvertise::find($id);
    //     $adminadvaertise->campaign_objective   =  $validatData['campaign_objective'];
    //     $adminadvaertise->campaign_name   =  $validatData['campaign_name'];
    //     $adminadvaertise->conversion_location   =  $validatData['conversion_location'];
    //     $adminadvaertise->performance_goal   =  $validatData['performance_goal'];
    //     $adminadvaertise->platforms   =  $validatData['platforms'];
    //     $adminadvaertise->budget_amount   =  $validatData['budget_amount'];
    //     $adminadvaertise->start_date   =  $validatData['start_date'];
    //     $adminadvaertise->end_date   =  $validatData['end_date'];
    //     $adminadvaertise->age   =  $validatData['age'];
    //     $adminadvaertise->gender   =  $validatData['gender'];
    //     $adminadvaertise->detail_targeting   =  $validatData['detail_targeting'];
    //     $adminadvaertise->country   =  $validatData['country'];
    //     $adminadvaertise->city   =  $validatData['city'];
    //     $adminadvaertise->device   =  $validatData['device'];
    //     $adminadvaertise->platform   =  $validatData['platform'];
    //     $adminadvaertise->inventory   =  $validatData['inventory'];
    //     $adminadvaertise->format   =  $validatData['format'];
    //     $adminadvaertise->primary_text   =  $validatData['primary_text'];
    //     $adminadvaertise->media   =  $validatData['media'];
    //     $adminadvaertise->heading   =  $validatData['heading'];
    //     $adminadvaertise->description   =  $validatData['description'];
    //     $adminadvaertise->call_to_action   =  $validatData['call_to_action'];
    //     $adminadvaertise->destination   =  $validatData['destination'];
    //     $adminadvaertise->tracking   =  $validatData['tracking'];
    //     $adminadvaertise->url_perimeter   =  $validatData['url_perimeter'];
    //     $adminadvaertise->number   =  $validatData['number'];
    //     $adminadvaertise->last_description   =  $validatData['last_description'];
    //     $adminadvaertise->status   =  $validatData['status'];

    //     $adminadvaertise->save();

    //     $data = DB::table('advertise_audience_files')->where('advertise_id', $id)->delete();

    //     if ($locaFiles = $validatData['advertise_audience_files']) {
    //         foreach ($locaFiles as $file) {
    //             // if(File::exists($file)){
    //             //     File::delete($file);
    //             // }
    //             $adaufile =  new AdvertiseAudienceFile();
    //             $adaufile->advertise_id  = $adminadvaertise->id;
    //             $adaufile->file    =  fileUpload($file, 'uploads/advertise_audience_files/', 500, 405);
    //             $adaufile->save();
    //         }
    //     }

    //     $data = DB::table('location_files')->where('advertise_id', $id)->delete();

    //     if ($locaFiles = $validatData['location_files']) {
    //         foreach ($locaFiles as $file) {
    //             // if(File::exists($file)){
    //             //     File::delete($file);
    //             // }
    //             $locatFile =  new LocationFile();
    //             $locatFile->advertise_id  = $adminadvaertise->id;
    //             $locatFile->file    =  fileUpload($file, 'uploads/location_files/', 500, 405);
    //             $locatFile->save();
    //         }
    //     }

    //     $data = DB::table('advertise_placements')->where('advertise_id', $id)->delete();
    //     $advertisePlace = new AdvertisePlacement();
    //     $advertisePlace->advertise_id  = $adminadvaertise->id;
    //     $advertisePlace->feeds  = request('feeds') ? request('feeds')  : '';
    //     $advertisePlace->story_reels  = request('story_reels') ? request('story_reels') : '';
    //     $advertisePlace->adds_video_and_reels  = request('adds_video_and_reels') ? request('adds_video_and_reels') : '';
    //     $advertisePlace->search_result  = request('search_result') ? request('search_result') : '';
    //     $advertisePlace->messages  = request('messages') ? request('messages') : '';
    //     $advertisePlace->apps_and_sites  = request('apps_and_sites') ? request('apps_and_sites') : '';
    //     $advertisePlace->save();

    //     return true;
    // }
}
