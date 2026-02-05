<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <form wire:submit="store" class="space-y-3">
        <!-- Basic Information Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl lg:border border-gray-200 dark:border-gray-700 p-2 lg:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2  gap-4 lg:gap-6 mb-0">
                    <div class="flex-1">
        <flux:field>
            <flux:label for="name" required>{{ __('messages.product.create.label_name') }} <span class="text-red-500">*</span></flux:label>
            <flux:input type="text" data-testid="name" id="name" wire:model="name" placeholder="Enter {{ __('messages.product.create.label_name') }}" required/>
            <flux:error name="name" data-testid="name_error"/>
        </flux:field>
    </div>
                             <div class="flex-1">
        <flux:field>
            <flux:label for="description" required>{{ __('messages.product.create.label_description') }} <span class="text-red-500">*</span></flux:label>
            <flux:textarea rows="3" wire:model="description" id="description" data-testid="description"  placeholder="Enter {{ __('messages.product.create.label_description') }}" required/>
            <flux:error name="description" data-testid="description_error"/>
        </flux:field>
    </div>
                             <div class="flex-1">
        <flux:field>
            <flux:label for="code" required>{{ __('messages.product.create.label_code') }} <span class="text-red-500">*</span></flux:label>
            <flux:input type="text" data-testid="code" id="code" wire:model="code" placeholder="Enter {{ __('messages.product.create.label_code') }}" required/>
            <flux:error name="code" data-testid="code_error"/>
        </flux:field>
    </div>
                             <div class="flex-1">
        <flux:field>
            <flux:label for="price" required>{{ __('messages.product.create.label_price') }} <span class="text-red-500">*</span></flux:label>
            <flux:input type="text" data-testid="price" id="price" wire:model="price" placeholder="Enter {{ __('messages.product.create.label_price') }}" required/>
            <flux:error name="price" data-testid="price_error"/>
        </flux:field>
    </div>
                             <x-flux.multi-select id="brand" model="brand" label="{{ __('messages.product.create.label_brands') }}" required>
        @if ($brands)  
@foreach ($brands as $value) <label class="flex items-center px-3 py-2 hover:bg-black-50 cursor-pointer transition" @click="open = false">
                            <input type="checkbox" class="mr-2 h-4 w-4 text-black-600 border-gray-300 rounded
                        focus:ring-black-500 focus:ring-2 cursor-pointer" wire:model="brand" value="{{ $value->id}}" ><span class="text-gray-700">{{$value->name}}</span></label>@endforeach 
@endif
    </x-flux.multi-select>
            </div>
        </div>

         

        <!-- Action Buttons -->
        <div class="flex items-center justify-top gap-3 mt-3 lg:mt-3 border-t-2 lg:border-none border-gray-100 py-4 lg:py-0">

            <flux:button type="submit" variant="primary" data-testid="submit_button" class="cursor-pointer h-8! lg:h-9!" wire:loading.attr="disabled" wire:target="store">
                {{ __('messages.update_button_text') }}
            </flux:button>

            <flux:button type="button" data-testid="cancel_button" class="cursor-pointer h-8! lg:h-9!" variant="outline" href="/product" wire:navigate>
                {{ __('messages.cancel_button_text') }}
            </flux:button>
        </div>
    </form>
</div>
