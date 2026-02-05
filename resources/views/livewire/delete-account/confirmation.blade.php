<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('messages.delete_account.title')"
        :description="__('messages.delete_account.final_confirmation_required')" />

    @if($errorMessage)
    <div
        x-data="{ show: true }"
        x-show="show"
        class="mb-4 flex items-center justify-between rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800 shadow dark:border-red-800 dark:bg-red-900/20 dark:text-red-200"
        role="alert">
        <span class="text-sm font-medium">{{ $errorMessage }}</span>
        <button type="button"
            class="ml-3 inline-flex h-6 w-6 items-center justify-center rounded-md text-red-700 hover:bg-red-100 focus:outline-none dark:text-red-300 dark:hover:bg-red-800"
            x-on:click="show = false">
            <x-flux::icon name="x-mark" class="h-4 w-4" />
        </button>
    </div>
    @endif

    <div class="flex flex-col gap-6">
        <!-- Warning Section -->
        <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('messages.delete_account.are_you_sure') }}</h3>
            <p class="text-zinc-600 dark:text-zinc-400">
                {!! __('messages.delete_account.permanent_action', [
                'permanent' => '<span class="font-semibold text-red-600 dark:text-red-400">' . __('messages.delete_account.permanent') . '</span>',
                'cannot_be_undone' => '<span class="font-semibold text-red-600 dark:text-red-400">' . __('messages.delete_account.cannot_be_undone') . '</span>'
                ]) !!}
                {{ __('messages.delete_account.data_removal_warning') }}
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <!-- Cancel Button -->
            <flux:button
                variant="ghost"
                class="flex-1 h-11 px-4 font-medium transition-all duration-200 border border-zinc-300 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-zinc-600 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 active:scale-[0.98] cursor-pointer"
                wire:click="cancel"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50 cursor-not-allowed"
                wire:target="cancel">
                <span wire:loading.remove wire:target="cancel" class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span>{{ __('messages.delete_account.cancel') }}</span>
                </span>
                <span wire:loading wire:target="cancel" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>{{ __('messages.delete_account.processing') }}</span>
                </span>
            </flux:button>

            <!-- Delete Button -->
            <flux:button
                variant="danger"
                class="flex-1 h-11 px-4 font-semibold transition-all duration-200 border border-red-600 dark:border-red-700 hover:border-red-700 dark:hover:border-red-600 hover:shadow-md active:scale-[0.98] cursor-pointer"
                wire:click="confirmDelete"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50 cursor-not-allowed"
                wire:target="confirmDelete">
                <span wire:loading.remove wire:target="confirmDelete" class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"></path>
                    </svg>
                    <span>{{ __('messages.delete_account.delete_account') }}</span>
                </span>
                <span wire:loading wire:target="confirmDelete" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>{{ __('messages.delete_account.deleting') }}</span>
                </span>
            </flux:button>
        </div>
    </div>
</div>