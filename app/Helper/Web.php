<?php
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

function slugCreate($modelName, $slug_text,$slugColumn='slug')
{
    $slug = Str::slug($slug_text, '-');
    $i = 1;
    while ($modelName::where($slugColumn, $slug)->exists()) {
        $slug = Str::slug($slug_text, '-') . '-' . $i++;
    }
    return $slug;
}

function slugUpdate($modelName, $slug_text, $modelId,$slugColumn='slug')
{
    $slug = Str::slug($slug_text, '-');
    $i = 1;
    while ($modelName::where($slugColumn, $slug)->where('id', '!=', $modelId)->exists()) {
        $slug = Str::slug($slug_text, '-') . '-' . $i++;
    }
    return $slug;
}

function fileUpload($file, $path,$withd = 400,$height=400)
{
    $image_name = uniqid() .'-'. time() . '.' . $file->getClientOriginalExtension();
    $imagePath = $path . '/' . $image_name;
    Image::make($file)->resize($withd, $height, function ($constraint) {
        $constraint->aspectRatio();
    })->save(public_path($imagePath));

    return $imagePath;
}

function orderId(){
    $timestamp = now()->format('YmdHis');
    $randomString = Str::random(6);
    return $timestamp . $randomString;
}
