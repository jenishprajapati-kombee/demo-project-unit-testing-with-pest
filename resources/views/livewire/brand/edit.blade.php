<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <form wire:submit="store" class="space-y-3">
        <!-- Basic Information Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl lg:border border-gray-200 dark:border-gray-700 p-2 lg:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2  gap-4 lg:gap-6 mb-0">
                    <div class="flex-1">
        <flux:field>
            <flux:label for="name" required>{{ __('messages.brand.create.label_name') }} <span class="text-red-500">*</span></flux:label>
            <flux:input type="text" data-testid="name" id="name" wire:model="name" placeholder="Enter {{ __('messages.brand.create.label_name') }}" required/>
            <flux:error name="name" data-testid="name_error"/>
        </flux:field>
    </div>
                             <div class="flex-1">
        <flux:field>
            <flux:label for="remark" >{{ __('messages.brand.create.label_remark') }} </flux:label>
            <flux:input type="text" data-testid="remark" id="remark" wire:model="remark" placeholder="Enter {{ __('messages.brand.create.label_remark') }}" />
            <flux:error name="remark" data-testid="remark_error"/>
        </flux:field>
    </div>
                         <div class="flex-1">
    <x-flux.date-time-picker wireModel='bob'
    for="bob"
    label="{{ __('messages.brand.create.label_bob') }}"
    :required="true"
    />
</div>
                             <div class="flex-1">
        <flux:field>
            <flux:label for="description" required>{{ __('messages.brand.create.label_description') }} <span class="text-red-500">*</span></flux:label>
            <flux:textarea rows="3" wire:model="description" id="description" data-testid="description"  placeholder="Enter {{ __('messages.brand.create.label_description') }}" required/>
            <flux:error name="description" data-testid="description_error"/>
        </flux:field>
    </div>
                              <x-flux.single-select id="country_id" label="{{ __('messages.brand.create.label_countries') }}" wire:model="country_id" data-testid="country_id" required searchable>
        <option value='' >Select {{ __('messages.brand.create.label_countries') }}</option>
   @if (!empty($countries))
       @foreach ($countries as $value) 
           <option value="{{ $value->id}}" >{{$value->name}}</option>
       @endforeach 
   @endif
    </x-flux.single-select>
                              <x-flux.single-select id="state_id" label="{{ __('messages.brand.create.label_states') }}" wire:model="state_id" data-testid="state_id" required searchable>
        <option value='' >Select {{ __('messages.brand.create.label_states') }}</option>
   @if (!empty($states))
       @foreach ($states as $value) 
           <option value="{{ $value->id}}" >{{$value->name}}</option>
       @endforeach 
   @endif
    </x-flux.single-select>
                              <x-flux.single-select id="city_id" label="{{ __('messages.brand.create.label_cities') }}" wire:model="city_id" data-testid="city_id" required searchable>
        <option value='' >Select {{ __('messages.brand.create.label_cities') }}</option>
   @if (!empty($cities))
       @foreach ($cities as $value) 
           <option value="{{ $value->id}}" >{{$value->name}}</option>
       @endforeach 
   @endif
    </x-flux.single-select>
                             <div class="flex-1">
        <flux:field>
            <flux:label for="status" required>{{ __('messages.brand.create.label_status') }} <span class="text-red-500">*</span></flux:label>
            <div class="flex gap-6">
            <div class="flex items-center cursor-pointer">
                        <input data-testid="status" type="radio" value="{{ config('constants.brand.status.key.active') }}" name="status" required wire:model="status" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
    <label for="status" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
        {{ config('constants.brand.status.value.active') }}
    </label>&nbsp;&nbsp;    <input data-testid="status" type="radio" value="{{ config('constants.brand.status.key.inactive') }}" name="status" required wire:model="status" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" />
    <label for="status" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
        {{ config('constants.brand.status.value.inactive') }}
    </label>&nbsp;&nbsp;
                </div>
            </div>
            <flux:error name="status" data-testid="status_error"/>
        </flux:field>
    </div>
            </div>
        </div>

         

        <!-- Action Buttons -->
        <div class="flex items-center justify-top gap-3 mt-3 lg:mt-3 border-t-2 lg:border-none border-gray-100 py-4 lg:py-0">

            <flux:button type="submit" variant="primary" data-testid="submit_button" class="cursor-pointer h-8! lg:h-9!" wire:loading.attr="disabled" wire:target="store">
                {{ __('messages.update_button_text') }}
            </flux:button>

            <flux:button type="button" data-testid="cancel_button" class="cursor-pointer h-8! lg:h-9!" variant="outline" href="/brand" wire:navigate>
                {{ __('messages.cancel_button_text') }}
            </flux:button>
        </div>
    </form>
</div>
