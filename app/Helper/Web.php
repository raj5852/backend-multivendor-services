<?php

use App\Models\Subscription;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

function slugCreate($modelName, $slug_text, $slugColumn = 'slug')
{
    $slug = Str::slug($slug_text, '-');
    $i = 1;
    while ($modelName::where($slugColumn, $slug)->exists()) {
        $slug = Str::slug($slug_text, '-') . '-' . $i++;
    }
    return $slug;
}

function slugUpdate($modelName, $slug_text, $modelId, $slugColumn = 'slug')
{
    $slug = Str::slug($slug_text, '-');
    $i = 1;
    while ($modelName::where($slugColumn, $slug)->where('id', '!=', $modelId)->exists()) {
        $slug = Str::slug($slug_text, '-') . '-' . $i++;
    }
    return $slug;
}

function fileUpload($file, $path, $withd = 400, $height = 400)
{
    $image_name = uniqid() . '-' . time() . '.' . $file->getClientOriginalExtension();
    $imagePath = $path . '/' . $image_name;
    Image::make($file)->resize($withd, $height, function ($constraint) {
        $constraint->aspectRatio();
    })->save(public_path($imagePath));

    return $imagePath;
}

function orderId()
{
    $timestamp = now()->format('YmdHis');
    $randomString = Str::random(6);
    return $timestamp . $randomString;
}

function responsejson($message, $data = "success")
{
    return response()->json(
        [
            'data' => $data,
            'message' => $message
        ]
    );
}

function userid()
{
    return auth()->user()->id;
}


function upload_image($filename, $width, $height,)
{
    $imagename = uniqid() . '.' . $filename->getClientOriginalExtension();
    $new_webp = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $imagename);

    Image::make($filename)->encode('webp', 90)->fit($width, $height)->save('assets/images/' . $new_webp);
    $image_upload = 'assets/images/' . $new_webp;
    return $image_upload;
}


function handleUpdatedUploadedImage($file, $path, $data, $delete_path, $field)
{
    $name = time() . $file->getClientOriginalName();

    $file->move(base_path('public/') . $path, $name);
    if ($data[$field] != null) {
        if (file_exists(base_path('public/') . $delete_path . $data[$field])) {
            unlink(base_path('public/') . $delete_path . $data[$field]);
        }
    }
    return $name;
}

if (!function_exists('uploadany_file')) {
    function uploadany_file($filename, $path = 'uploads/support/')
    {
        $uploadPath = $path;
        $i = 1;

        $extension = $filename->getClientOriginalExtension();
        $name =  uniqid() . $i++ . '.' . $extension;
        $filename->move($uploadPath, $name);

        return $uploadPath . $name;
    }
}


function userrole($roleid)
{
    if ($roleid == 2) {
        return "vendor";
    }
    if ($roleid == 3) {
        return "affiliate";
    }
    if ($roleid == 4) {
        return "user";
    }
}

function convertfloat($originalNumber)
{
    return str_replace(',', '', $originalNumber);
}

function membershipexpiredate($value)
{
    if ($value == 'monthly') {
        return now()->addMonth(1);
    } elseif ($value == 'half_yearly') {
        return now()->addMonth(6);
    } elseif ($value == 'yearly') {
        return now()->addYear(1);
    }
}

function getmonth($monthname)
{
    if ($monthname == 'monthly') {
        return 1;
    } elseif ($monthname == 'half_yearly') {
        return 6;
    } elseif ($monthname == 'yearly') {
        return 12;
    }
}

function ismembershipexists($userid = null)
{
    if (!$userid) {
        $userid = auth()->id();
    }
    return UserSubscription::where(['user_id' => $userid])->exists();
}

function isactivemembership($userid = null)
{
    if (!$userid) {
        $userid = auth()->id();
    }
    $usersubscription =  UserSubscription::where(['user_id' => $userid])->first();
    $sub = Subscription::find($usersubscription->subscription_id);
    if ($sub->subscription_amount != 0) {
        $date =  Carbon::parse($usersubscription->expire_date)->addMonth(1);
        if ($date > now()) {
            return 1;
        }
        return;
    } else {
        $freesubscriptiondate =  Carbon::parse($usersubscription->expire_date);
        if ($freesubscriptiondate > now()) {
            return 1;
        }
        return;
    }
}


function getmembershipdetails($userid = null)
{
    if (!$userid) {
        $userid = auth()->id();
    }

    return UserSubscription::where(['user_id' => $userid])->first();
}

function paymentredirect($role)
{
    if (userrole($role) == 'vendor') {
        return 'vendors-dashboard';
    }
    if (userrole($role) == 'affiliate') {
        return 'affiliates-dashboard';
    }
    if (userrole($role) == 'user') {
        return 'user-dashboard';
    }
}
