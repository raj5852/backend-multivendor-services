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
        $users = DB::table('settings')->where('deleted_at', null)->first();
        return $this->response($users);
    }

    public function companion()
    {
        $companions = DB::table('companions')->where('deleted_at', null)->take(3)->get();
        return $this->response($companions);
    }

    public function faq()
    {
        $faqs = DB::table('faqs')->get();
        return $this->response($faqs);
    }

    public function fottermedia()
    {
        $footermedia = DB::table('footer_media')->where('deleted_at', null)->take(8)->get();
        return $this->response($footermedia);
    }

    public function members()
    {
        $members = DB::table('members')->take(8)->where('deleted_at', null)->get();
        return $this->response($members);
    }

    public function mission()
    {
        $mission = DB::table('missions')->where('deleted_at', null)->take(4)->get();

        return $this->response($mission);
    }

    public function orgOne()
    {
        $organizations = DB::table('organizations')->where('deleted_at', null)->take(4)->get();
        return $this->response($organizations);
    }

    public function orgTwo()
    {
        $organizationtwos = DB::table('organization_twos')->where('deleted_at', null)->take(4)->get();
        return $this->response($organizationtwos);
    }

    public function service()
    {
        $service = DB::table('our_services')->take(3)->where('deleted_at', null)->get();
        return $this->response($service);
    }
    public function Itservice()
    {
        $service = DB::table('itservices')->take(6)->where('deleted_at', null)->get();
        return $this->response($service);
    }

    public function partner()
    {
        $partners = DB::table('partners')->where('deleted_at', null)->get();
        return $this->response($partners);
    }

    public function testimonial()
    {
        $testimonial = DB::table('testimonials')->where('deleted_at', null)->get();
        return $this->response($testimonial);
    }

}

