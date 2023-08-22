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




     function upload_image($filename, $width, $height,)
    {
        $imagename = uniqid().'.'.$filename->getClientOriginalExtension();
        $new_webp = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $imagename);

        Image::make($filename)->encode('webp', 90)->fit($width, $height)->save('assets/images/'.$new_webp);
        $image_upload = 'assets/images/'.$new_webp;
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

    function convertToBanglaNumber($number) {
        $english = array('0','1','2','3','4','5','6','7','8','9');
        $bangla = array('০','১','২','৩','৪','৫','৬','৭','৮','৯');

        $banglaNumber = str_replace($english, $bangla, $number);
        return $banglaNumber;
    }


//    function setting() {
//         return Setting::first();
//     }
