<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    public $id;

    public $user;

    public $event = 'showuserInfoModal';

    #[On('show-user-info')]
    public function show($id)
    {
        $this->user = null;

        $this->user = User::select(
            'users.id',
            'users.name',
            'users.email',
            'users.password',
            'roles.name as role_name',
            'users.dob',
            'users.profile',
            'countries.name as country_name',
            'states.name as state_name',
            'cities.name as city_name',
            DB::raw(
                '(CASE
                                        WHEN users.gender = "' . config('constants.user.gender.key.female') . '" THEN  "' . config('constants.user.gender.value.female') . '"
                                        WHEN users.gender = "' . config('constants.user.gender.key.male') . '" THEN  "' . config('constants.user.gender.value.male') . '"
                                ELSE " "
                                END) AS gender'
            ),
            DB::raw(
                '(CASE
                                        WHEN users.status = "' . config('constants.user.status.key.active') . '" THEN  "' . config('constants.user.status.value.active') . '"
                                        WHEN users.status = "' . config('constants.user.status.key.inactive') . '" THEN  "' . config('constants.user.status.value.inactive') . '"
                                ELSE " "
                                END) AS status'
            ),
            'users.email_verified_at',
            'users.remember_token',
            'users.locale',
            DB::raw('GROUP_CONCAT(DISTINCT user_tags.keyword) AS users_keyword')
        )
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('countries', 'countries.id', '=', 'users.country_id')
            ->leftJoin('states', 'states.id', '=', 'users.state_id')
            ->leftJoin('cities', 'cities.id', '=', 'users.city_id')
            ->leftJoin('user_tags', 'user_tags.user_id', '=', 'users.id')
            ->where('users.id', $id)
            ->groupBy('users.id')
            ->first();

        if (! is_null($this->user)) {
            $this->dispatch('show-modal', id: '#' . $this->event);
        } else {
            session()->flash('error', __('messages.user.messages.record_not_found'));
        }
    }

    public function render()
    {
        return view('livewire.user.show');
    }
}
