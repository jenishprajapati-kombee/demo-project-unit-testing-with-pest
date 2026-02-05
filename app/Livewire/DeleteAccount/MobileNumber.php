<?php

namespace App\Livewire\DeleteAccount;

use App\Helper;
use App\Models\WebUser;
use App\Rules\ReCaptcha;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class MobileNumber extends Component
{
    #[Validate('required|string|email|max:191')]
    public string $email = '';

    public $recaptchaToken;

    public $errorMessage = '';

    public function mount()
    {
        // Rate limiting for email page
        $request = request();
        $visitorId = $request->cookie('visitor_id');

        if (! $visitorId) {
            $visitorId = bin2hex(random_bytes(16));
            cookie()->queue(cookie('visitor_id', $visitorId, 60 * 24 * 30));
        }
        $key = md5(($visitorId ?: $request->ip()) . '|' . $request->header('User-Agent'));

        if (RateLimiter::tooManyAttempts($key, 10)) {
            abort(429);
        }

        RateLimiter::hit($key, 60);
    }

    public function checkEmail()
    {
        $this->validate(['email' => 'required|string|email|max:191']);

        if (App::environment(['production', 'uat'])) {
            $recaptchaResponse = ReCaptcha::verify($this->recaptchaToken);
            if (! $recaptchaResponse['success']) {
                $this->errorMessage = __('messages.login.recaptchaError');
                Helper::logInfo(static::class, __FUNCTION__, __('messages.login.recaptchaError'), ['email' => $this->email]);

                return;
            }
        }

        $email = Str::lower(trim($this->email));
        $user = WebUser::where('email', $email)->first();

        if (! $user) {
            $this->errorMessage = __('messages.app_login.account_not_exist');
            Helper::logInfo(static::class, __FUNCTION__, 'Email does not exist in system.', ['email' => $email]);

            return;
        }

        // Check if user is active
        if ($user->status !== config('constants.status.active')) {
            $this->errorMessage = __('messages.app_login.inactive_account');
            Helper::logInfo(static::class, __FUNCTION__, 'User account is inactive.', ['email' => $email]);

            return;
        }

        // Check if user is deleted
        if ($user->deleted_at) {
            $this->errorMessage = __('messages.app_login.inactive_account');
            Helper::logInfo(static::class, __FUNCTION__, 'User account is deleted.', ['email' => $email]);

            return;
        }

        // Email exists and user is valid, redirect to password page
        $this->redirect(route('delete-account.verify_otp_file', ['email' => $email]), navigate: true);
    }

    public function render()
    {
        return view('livewire.delete-account.mobile-number')->title(__('messages.delete_account.page_title'));
    }
}
