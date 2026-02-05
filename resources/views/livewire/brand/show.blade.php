<div>
    <x-show-info-modal modalTitle="{{ __('messages.brand.show.label_brand') }}" :eventName="$event" :showSaveButton="false" :showCancelButton="false">
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <flux:field class="border-b border-gray-200 dark:border-gray-700 gap-1!">
        <flux:label>{{ __('messages.brand.show.details.name') }}</flux:label>
        <flux:description>{{ $brand?->name ?? '-' }}</flux:description>
    </flux:field>
                             <flux:field class="border-b border-gray-200 dark:border-gray-700 gap-1!">
        <flux:label>{{ __('messages.brand.show.details.remark') }}</flux:label>
        <flux:description>{{ $brand?->remark ?? '-' }}</flux:description>
    </flux:field>
                             <flux:field class="border-b border-gray-200 dark:border-gray-700 gap-1!">
        <flux:label>{{ __('messages.brand.show.details.bob') }}</flux:label>
        <flux:description>{{ !is_null($brand) && !is_null($brand->bob)
            ? Carbon\Carbon::parse($brand->bob)->format(config('constants.default_datetime_format'))
            : '-' }}</flux:description>
    </flux:field>
                             <flux:field class="border-b border-gray-200 dark:border-gray-700 gap-1!">
        <flux:label>{{ __('messages.brand.show.details.description') }}</flux:label>
        <flux:description>{{ $brand?->description ?? '-' }}</flux:description>
    </flux:field>
                             <flux:field class="border-b border-gray-200 dark:border-gray-700 gap-1!">
        <flux:label>{{ __('messages.brand.show.details.country_name') }}</flux:label>
        <flux:description>{{ !is_null($brand) ? $brand->country_name : '-' }}</flux:description>
    </flux:field>
                             <flux:field class="border-b border-gray-200 dark:border-gray-700 gap-1!">
        <flux:label>{{ __('messages.brand.show.details.state_name') }}</flux:label>
        <flux:description>{{ !is_null($brand) ? $brand->state_name : '-' }}</flux:description>
    </flux:field>
                             <flux:field class="border-b border-gray-200 dark:border-gray-700 gap-1!">
        <flux:label>{{ __('messages.brand.show.details.city_name') }}</flux:label>
        <flux:description>{{ !is_null($brand) ? $brand->city_name : '-' }}</flux:description>
    </flux:field>
                             <flux:field class="border-b border-gray-200 dark:border-gray-700 gap-1!">
        <flux:label>{{ __('messages.brand.show.details.status') }}</flux:label>
        <flux:description>{{ $brand?->status ?? '-' }}</flux:description>
    </flux:field>
            </div>
        </div>
    </x-show-info-modal>
</div>
