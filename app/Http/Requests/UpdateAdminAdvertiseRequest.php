<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateAdminAdvertiseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'campaign_objective'  => ['required'],
            'campaign_name'  => ['required', 'max:256'],
            'conversion_location'  => ['required'],
            'performance_goal'  => ['required'],
            'platforms'  => ['required'],
            'budget_amount'  => ['required'],
            'start_date'  => ['required'],
            'end_date'  => ['required'],
            'age'  => ['required', 'numeric'],
            'gender'  => ['required'],
            'detail_targeting'  => ['required'],
            'country'  => ['required'],
            'city'  => ['required'],
            'device'  => ['required'],
            'platform'  => ['required'],
            'inventory'  => ['required'],
            'format'  => ['required'],
            'primary_text'  => ['required'],
            'media'  => ['required', 'url'],
            'heading'  => ['required'],
            'description'  => ['required'],
            'call_to_action'  => ['required'],
            'destination'  => ['required'],
            'tracking'  => ['required'],
            'url_perimeter'  => ['required', 'url'],
            'number'  => ['required','numeric'],
            'last_description'  => ['required'],
            'status'  => ['required','in:pending,progress,cancel,complited'],

            'advertise_audience_files' => 'nullable',
            'location_files'   => 'nullable',

            'feeds' => 'nullable',
            'story_reels' => 'nullable',
            'adds_video_and_reels' => 'nullable',
            'search_result' => 'nullable',
            'messages' => 'nullable',
            'apps_and_sites' => 'nullable',

        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }
}
