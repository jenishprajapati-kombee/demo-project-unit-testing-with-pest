<?php

namespace App\Http\Resources;

use App\Models\WebUser;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin WebUser
 */
class WebUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id ?? '',
            'name' => $this->name ?? '',
            'email' => $this->email ?? '',
            'password' => $this->password ?? '',
            'dob' => $this->dob ? (is_string($this->dob) ? Carbon::parse($this->dob)->format(config('constants.api_date_format')) : $this->dob->format(config('constants.api_date_format'))) : '',
            'country_id' => $this->country_id ?? '',
            'state_id' => $this->state_id ?? '',
            'city_id' => $this->city_id ?? '',
            'gender' => $this->gender ?? '',
            'gender_text' => config('constants.gender_values.' . $this->gender) ?? '',
            'status' => $this->status ?? '',
            'status_text' => config('constants.status_values.' . $this->status) ?? '',
        ];
    }
}
