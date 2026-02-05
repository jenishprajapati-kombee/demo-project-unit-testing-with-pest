<div>
    <x-show-info-modal modalTitle="{{ __('messages.user.show.label_user') }}" :eventName="$event" :showSaveButton="false" :showCancelButton="false">
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <flux:field class="border-b border-gray-200 dark:border-gray-700 gap-1!">
        <flux:label>{{ __('messages.user.show.details.name') }}</flux:label>
        <flux:description>{{ $user?->name ?? '-' }}</flux:description>
    </flux:field>
                             <flux:field class="border-b border-gray-200 dark:border-gray-700 gap-1!">
        <flux:label>{{ __('messages.user.show.details.email') }}</flux:label>
        <flux:description>{{ $user?->email ?? '-' }}</flux:description>
    </flux:field>
                             <flux:field class="border-b border-gray-200 dark:border-gray-700 gap-1!">
        <flux:label>{{ __('messages.user.show.details.role_name') }}</flux:label>
        <flux:description>{{ !is_null($user) ? $user->role_name : '-' }}</flux:description>
    </flux:field>
                             <flux:field class="border-b border-gray-200 dark:border-gray-700 gap-1!">
        <flux:label>{{ __('messages.user.show.details.dob') }}</flux:label>
        <flux:description>{{ !is_null($user) && !is_null($user->dob)
            ? Carbon\Carbon::parse($user->dob)->format(config('constants.default_date_format'))
            : '-' }}</flux:description>
    </flux:field>
                             <flux:field class="border-b border-gray-200 dark:border-gray-700 gap-1!">
        <flux:label>{{ __('messages.user.show.details.profile') }}</flux:label>
        <flux:description>{!! isset($user) && $user->profile !='' ? '<a target="_blank" class="btn btn-light-info" href="' . $user->profile . '">View Image <i class="las la-file-image fs-4 me-2"></i></a>' : '-' !!}</flux:description>
    </flux:field>
                             <flux:field class="border-b border-gray-200 dark:border-gray-700 gap-1!">
        <flux:label>{{ __('messages.user.show.details.country_name') }}</flux:label>
        <flux:description>{{ !is_null($user) ? $user->country_name : '-' }}</flux:description>
    </flux:field>
                             <flux:field class="border-b border-gray-200 dark:border-gray-700 gap-1!">
        <flux:label>{{ __('messages.user.show.details.state_name') }}</flux:label>
        <flux:description>{{ !is_null($user) ? $user->state_name : '-' }}</flux:description>
    </flux:field>
                             <flux:field class="border-b border-gray-200 dark:border-gray-700 gap-1!">
        <flux:label>{{ __('messages.user.show.details.city_name') }}</flux:label>
        <flux:description>{{ !is_null($user) ? $user->city_name : '-' }}</flux:description>
    </flux:field>
                             <flux:field class="border-b border-gray-200 dark:border-gray-700 gap-1!">
        <flux:label>{{ __('messages.user.show.details.gender') }}</flux:label>
        <flux:description>{{ $user?->gender ?? '-' }}</flux:description>
    </flux:field>
                             <flux:field class="border-b border-gray-200 dark:border-gray-700 gap-1!">
        <flux:label>{{ __('messages.user.show.details.status') }}</flux:label>
        <flux:description>{{ $user?->status ?? '-' }}</flux:description>
    </flux:field>
                             <flux:field class="border-b border-gray-200 dark:border-gray-700 gap-1!">
        <flux:label>{{ __('messages.user.show.details.users_keyword') }}</flux:label>
        <flux:description>{{ !is_null($user) ? $user->users_keyword : '-' }}</flux:description>
    </flux:field>
            </div>
        </div>
    </x-show-info-modal>
</div>
