<?php

namespace App\Services\Vendor;

use App\Enums\Status;
use App\Models\ServiceImages;
use App\Models\ServicePackage;
use App\Models\VendorService;

/**
 * Class ProductService.
 */
class ProductService
{
    static function store($data)
    {
        $vendorServie = new VendorService;
        $vendorServie->user_id = userid();
        $vendorServie->service_category_id =  $data['service_category_id'];
        $vendorServie->service_sub_category_id =  $data['service_sub_category_id'];
        $vendorServie->service_sub_category_id =  $data['service_sub_category_id'];
        $vendorServie->rating =  0.0;
        $vendorServie->title =   $data['title'];
        $vendorServie->description =   $data['description'];
        $vendorServie->tags =   $data['tags'];
        $vendorServie->contract =   $data['contract'];
        $vendorServie->status =   Status::Pending->value;
        $vendorServie->commission =   $data['commission'];
        $vendorServie->commission_type =   $data['commission_type'];
        if (request()->hasFile('image')) {
            $vendorServie->image =  fileUpload(request()->file('image'), 'uploads/vendor');
        }
        $vendorServie->save();


        // foreach (request('package_title') as $key => $value) {
        //     $servicepackages = new ServicePackage();
        //     $servicepackages->vendor_service_id = $vendorServie->id;
        //     $servicepackages->time =  $data['time'][$key];
        //     $servicepackages->package_title =  $data['package_title'];
        //     $servicepackages->package_description =  $data['package_description'][$key];
        //     $servicepackages->price =  $data['price'][$key];
        //     $servicepackages->save();
        // }
        foreach (request('package_title') as $key => $value) {
            $servicepackages = new ServicePackage();
            $servicepackages->vendor_service_id = $vendorServie->id;
            $servicepackages->time =  request('time')[$key];
            $servicepackages->package_title = $value;
            $servicepackages->package_description = request('package_description')[$key];
            $servicepackages->price =  1;
            $servicepackages->save();
        }

        // foreach ($data['images'] as $key => $value) {
        //     $serviceImages =  new ServiceImages();
        //     $serviceImages->vendor_service_id = $vendorServie->id;

        //     if (request()->hasFile($value)) {
        //         $serviceImages->image = fileUpload($value,'uploads/vendor');
        //     }
        //     $serviceImages->save();
        // }

        return true;
    }
}
