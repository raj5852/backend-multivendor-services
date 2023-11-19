<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Settings extends Model
{
    use HasFactory, SoftDeletes;

    // protected $guarded = [];

    protected $fillable = [
        'logo',
        'home_banner_heading',
        'home_banner_description',
        'service_one_title',
        'service_one_heading',
        'org_one_title',
        'org_one_heading',
        'org_one_photo',
        'org_one_video_link',
        'count_one',
        'one_title',
        'count_two_title',
        'count_three',
        'count_three_title',
        'count_four',
        'count_four_title',
        'service_two_title',
        'service_two_heading',
        'org_title',
        'org_heading',
        'org_photo',
        'chose_us_title',
        'chose_us_heading',
        'chose_description',
        'progress_title',
        'progress_value',
        'progres_two_title',
        'progres_two_value',
        'progres_three_title',
        'progres_three_value',
        'progres_four_title',
        'progres_four_value',
        'chose_us_two_title',
        'chose_us_two_heading',
        'testimonial_title',
        'testimonial_heading',
        'chose_card_one_icon',
        'chose_card_one_title',
        'chose_card_one_description',
        'chose_card_two_icon',
        'chose_card_two_title',
        'chose_card_two_description',
        'chose_card_three_icon',
        'chose_card_three_title',
        'chose_card_three_description',
        'chose_card_four_icon',
        'chose_card_four_title',
        'chose_card_four_description',
        'partner_title',
        'partner_heading',
        'newsletter_title',
        'newsletter_description',
        'footer_description',
        'footer_contact_address',
        'footer_contact_number',
        'footer_image',
        'copywright_text',
        'credit_name',
        'credit_link',
        'service_banner_heading',
        'service_banner_description',
        'advertise_banner_heading',
        'advertise_banner_description',
        'advertise_banner_image',
        'overview_title',
        'overview_description',
        'get_sarted_title',
        'get_sarted_description',
        'about_banner_title',
        'about_banner_heading',
        'about_banner_description',
        'about_banner_image',
        'about_banner_increment_one_count',
        'about_banner_increment_one_title',
        'about_banner_increment_two_count',
        'about_banner_increment_otwo_title',
        'about_banner_increment_othree_count',
        'about_banner_increment_three_title',
        'vision_title',
        'vision_heading',
        'vision_description',
        'vision_image_one',
        'vision_image_two',
        'vision_image_three',
        'mission_title',
        'mission_heading',
        'mission_description',
        'subscription_heading',
        'subscription_title',
        'count_two',
        'member_title',
        'member_heading',
        'home_banner_description',
        'chose_card_one_description',
        'chose_card_two_description',
        'chose_card_three_description',
        'mission_image',
        'tag_manager',
        'is_advance'
    ];
}
