<div class="flex flex-col gap-6 max-w-2xl mx-auto">
    <x-auth-header 
        :title="__('messages.delete_account.title')" 
        :description="__('messages.delete_account.read_info_description')" 
    />

    @if($errorMessage)
        <div
            x-data="{ show: true }"
            x-show="show"
            class="mb-4 flex items-center justify-between rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800 shadow dark:border-red-800 dark:bg-red-900/20 dark:text-red-200"
            role="alert"
        >
            <span class="text-sm font-medium">{{ $errorMessage }}</span>
            <button type="button"
                class="ml-3 inline-flex h-6 w-6 items-center justify-center rounded-md text-red-700 hover:bg-red-100 focus:outline-none dark:text-red-300 dark:hover:bg-red-800"
                x-on:click="show = false">
                <x-flux::icon name="x-mark" class="h-4 w-4" />
            </button>
        </div>
    @endif

    <div class="flex flex-col gap-6">
        <!-- Data Security Section -->
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('messages.delete_account.ensuring_data_security_title') }}</h3>
            <p class="text-zinc-600 dark:text-zinc-400">
                {{ __('messages.delete_account.ensuring_data_security_description') }}
            </p>
        </div>

        <!-- Account Deletion Section -->
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('messages.delete_account.account_deletion_title') }}</h3>
            <p class="text-zinc-600 dark:text-zinc-400">
                {{ __('messages.delete_account.account_deletion_description') }}
            </p>
        </div>

        <div class="flex items-center justify-end">
            <flux:button 
                variant="primary" 
                class="w-full cursor-pointer" 
                wire:click="understand"
                wire:loading.attr="disabled" 
                data-test="understand-button" 
                wire:loading.class="opacity-50" 
                wire:target="understand" 
                id="understand-button"
            >
                <span wire:loading.remove wire:target="understand">{{ __('messages.delete_account.yes_i_understand') }}</span>
                <span wire:loading wire:target="understand">{{ __('messages.delete_account.processing') }}</span>
            </flux:button>
        </div>
    </div>
</div>

