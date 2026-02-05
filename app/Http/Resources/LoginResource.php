<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\WebUser
 *
 * @property string|null $authorization
 * @property string|null $refresh_token
 * @property \Carbon\CarbonInterface|string|null $token_expires_at
 */
class LoginResource extends JsonResource
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
            'dob' => $this->dob ? (is_string($this->dob) ? \Carbon\Carbon::parse($this->dob)->format(config('constants.api_date_format')) : $this->dob->format(config('constants.api_date_format'))) : '',
            'country_id' => $this->country_id ?? '',
            'state_id' => $this->state_id ?? '',
            'city_id' => $this->city_id ?? '',
            'gender' => $this->gender ?? '',
            'gender_text' => config('constants.gender_values.' . $this->gender) ?? '',
            'status' => $this->status ?? '',
            'status_text' => config('constants.status_values.' . $this->status) ?? '',
            'authorization' => $this->authorization ?? null,
            'refresh_token' => $this->refresh_token ?? null,
            'token_expires_at' => $this->token_expires_at ? (is_string($this->token_expires_at) ? \Carbon\Carbon::parse($this->token_expires_at)->format(config('constants.api_datetime_format')) : $this->token_expires_at->format(config('constants.api_datetime_format'))) : '',
        ];
    }
}
