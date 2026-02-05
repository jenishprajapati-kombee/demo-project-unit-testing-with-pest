<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('messages.delete_account.title')"
        :description="__('messages.delete_account.enter_email_description')" />

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

    <form wire:submit="checkEmail" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('messages.delete_account.email_address_label')"
            type="email"
            required
            autofocus
            :placeholder="__('messages.delete_account.email_address_placeholder')"
            data-testid="email"
            id="email"
            onblur="value=value.trim()" />

        <input type="hidden" id="recaptcha-token" name="recaptcha_token" wire:model="recaptchaToken">

        <div class="flex items-center justify-end">
            <flux:button
                variant="primary"
                class="w-full cursor-pointer"
                type="submit"
                wire:loading.attr="disabled"
                data-test="check-email-button"
                wire:loading.class="opacity-50"
                wire:target="checkEmail"
                id="check-email-button">
                <span wire:loading.remove wire:target="checkEmail">{{ __('messages.delete_account.continue_button') }}</span>
                <span wire:loading wire:target="checkEmail">{{ __('messages.delete_account.processing') }}</span>
            </flux:button>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js?render={{ config('constants.google_recaptcha_key') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle form submission with reCAPTCHA
        document.getElementById('check-email-button')?.addEventListener('click', function(e) {
            e.preventDefault();

            grecaptcha.ready(function() {
                grecaptcha.execute("{{ config('constants.google_recaptcha_key') }}", {
                    action: 'check_email'
                }).then(function(token) {
                    @this.set('recaptchaToken', token).then(function() {
                        @this.call('checkEmail');
                    });
                });
            });
        });
    });
</script>
@endpush