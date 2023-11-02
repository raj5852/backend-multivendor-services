<?php

use App\Enums\Status;
use App\Http\Controllers\TestController;
use App\Models\AdminAdvertise;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\SupportBox;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return "API for SOS projec :)";
});

Auth::routes();


Route::get('migrate', function () {
    Artisan::call('migrate');
});

Route::get('rollback', function () {
    Artisan::call('migrate:rollback', [
        '--step' => 1,
    ]);
});

Route::get('seed', function () {
    Artisan::call('db:seed');
});




Route::get('demo', function () {
    return array (
        'campaign_objective' => 'awareness',
        'campaign_name' => 'dasd',
        'conversion_location' => 'Website',
        'performance_goal' => 'performance_goal-3',
        'budget' => 'budget-2',
        'budget_amount' => '1',
        'start_date' => '24-11-2023',
        'end_date' => '18-11-2023',
        'audience' => 'audience-3',
        'age' => '19',
        'gender' => 'female',
        'detail_targeting' => 'ewe',
        'country' => 'Afghanistan',
        'city' => 'Badghis',
        'device' => 'device-2',
        'platform' => 'platform-1',
        'inventory' => 'full-inventory-2',
        'format' => 'format-2',
        'destination' => 'ds',
        'tracking' => 'ds',
        'url_perimeter' => 'https://bazar64.xyz/advertiserForm#top-advertise',
        'number' => '1111111111',
        'last_description' => 'ewewe',
        'paymethod' => 'my-wallet',
        'status' => 'pending',
        'placements' =>
        array (
          0 =>
          array (
            'feeds' => 'feeds-3',
          ),
          1 =>
          array (
            'story_reels' => 'story_reels-1',
          ),
          2 =>
          array (
            'adds_video_and_reels' => 'adds_video_and_reels-1',
          ),
          3 =>
          array (
            'search_result' => 'search_result-1',
          ),
          4 =>
          array (
            'messages' => 'messages-1',
          ),
          5 =>
          array (
            'apps_and_sites' => 'apps_and_sites-1',
          ),
        ),
        'ad_creative' =>
        array (
          0 =>
          array (
            'primary_text' => 'ew',
            'media' => 'ewe',
            'heading' => 'ewe',
            'description' => 'ew',
            'call_to_action' => 'Shop 3',
            'id' => '0',
          ),
        ),
        'advertise_audience_files' =>
        array (
          0 =>
          (array(
             'test' => false,
             'originalName' => 'pexels-dayan-rodio-4132936.jpg',
             'mimeType' => 'image/jpeg',
             'error' => 0,
             'hashName' => NULL,
          )),
        ),
        'location_files' =>
        array (
          0 =>
          (array(
             'test' => false,
             'originalName' => 'pexels-dayan-rodio-4132936.jpg',
             'mimeType' => 'image/jpeg',
             'error' => 0,
             'hashName' => NULL,
          )),
        ),
      ) ;
});


// Route::post('test', [TestController::class, 'index'])->name('test');
