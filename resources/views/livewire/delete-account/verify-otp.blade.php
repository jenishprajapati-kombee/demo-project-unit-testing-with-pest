<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('messages.delete_account.title')"
        :description="__('messages.delete_account.enter_password_description')" />

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

    <form wire:submit="verifyPassword" class="flex flex-col gap-6">
        <!-- Email Display (Read-only) -->
        <div>
            <flux:label>{{ __('messages.delete_account.email_address_label') }}</flux:label>
            <flux:input
                type="email"
                value="{{ $email }}"
                disabled
                readonly />
        </div>

        <!-- Password Input -->
        <div class="relative">
            <flux:input
                wire:model="password"
                :label="__('messages.delete_account.password_label')"
                type="password"
                required
                autofocus
                autocomplete="current-password"
                :placeholder="__('messages.delete_account.password_placeholder')"
                viewable
                data-testid="password"
                id="password" />
            @error('password')
            <flux:error name="password" />
            @enderror
        </div>

        <div class="flex items-center justify-end">
            <flux:button
                variant="primary"
                class="w-full cursor-pointer"
                type="submit"
                wire:loading.attr="disabled"
                data-test="verify-password-button"
                wire:loading.class="opacity-50"
                wire:target="verifyPassword"
                id="verify-password-button">
                <span wire:loading.remove wire:target="verifyPassword">{{ __('messages.delete_account.continue_button') }}</span>
                <span wire:loading wire:target="verifyPassword">{{ __('messages.delete_account.verifying') }}</span>
            </flux:button>
        </div>
    </form>
</div>