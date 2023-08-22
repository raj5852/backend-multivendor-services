<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        $users = DB::table('settings')->first();
        return $this->response($users);
    }

    public function companion()
    {
        $companions = DB::table('companions')->get();
        return $this->response($companions);
    }

    public function faq()
    {
        $faqs = DB::table('faqs')->get();
        return $this->response($faqs);
    }

    public function fottermedia()
    {
        $footermedia = DB::table('footer_media')->get();
        return $this->response($footermedia);
    }

    public function members()
    {
        $members = DB::table('members')->get();
        return $this->response($members);
    }

    public function mission()
    {
        $mission = DB::table('missions')->get();
        return $this->response($mission);
    }

    public function orgOne()
    {
        $organizations = DB::table('organizations')->get();
        return $this->response($organizations);
    }

    public function orgTwo()
    {
        $organizationtwos = DB::table('organization_twos')->get();
        return $this->response($organizationtwos);
    }

    public function service()
    {
        $service = DB::table('our_services')->get();
        return $this->response($service);
    }

    public function partner()
    {
        $partners = DB::table('partners')->get();
        return $this->response($partners);
    }

    public function testimonial()
    {
        $testimonial = DB::table('testimonials')->get();
        return $this->response($testimonial);
    }

}

