<div class="flex flex-col gap-6 max-w-2xl mx-auto">
    <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="text-center">
            <div class="mb-6">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900">
                    <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            
            <flux:heading size="xl" class="mb-4">{{ __('messages.delete_account.account_deleted_successfully') }}</flux:heading>
            <p class="text-zinc-600 dark:text-zinc-400 text-lg mb-6">
                {{ __('messages.delete_account.account_deleted_message') }}<br>
                {{ __('messages.delete_account.sorry_to_see_you_go') }}
            </p>
        </div>
    </div>
</div>
