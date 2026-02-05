<?php

namespace App\Livewire\Auth;

use App\Helper;
use App\Rules\ReCaptcha;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|email|max:191')]
    public string $email = '';

    #[Validate('required|string|min:6|max:191')]
    public string $password = '';

    public $recaptchaToken;

    public function mount()
    {
        // Rate limiting for login page - 10 times in 60 seconds - Start
        $request = request();
        $visitorId = $request->cookie('visitor_id');

        if (!$visitorId) {
            $visitorId = bin2hex(random_bytes(16));
            // Set the cookie for 30 days
            cookie()->queue(cookie('visitor_id', $visitorId, 60 * 24 * 30));
        }
        $key = md5(($visitorId ?: $request->ip()) . '|' . $request->header('User-Agent'));

        if (RateLimiter::tooManyAttempts($key, 10)) {
            abort(429);
        }

        RateLimiter::hit($key, 60);
        // Rate limiting for login page - 10 times in 60 seconds - End

        $this->dispatch('autoFocusElement', elId: 'email');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login()
    {
        $this->email = str_replace(' ', '', $this->email);

        $this->validate();

        if (App::environment(['production', 'uat'])) {
            $recaptchaResponse = ReCaptcha::verify($this->recaptchaToken);
            if (!$recaptchaResponse['success']) {
                $this->clearForm(); // clear all form data
                Helper::logInfo(static::class, __FUNCTION__, __('messages.login.recaptchaError'), ['email' => $this->email]);
                session()->flash('error', __('messages.login.recaptchaError'));

                return;
            }
        }

        $email = Str::lower($this->email);

        $credentials = [
            'email' => $email,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials)) { // User Found
            $user = Auth::user();

            // Use Laravel session to avoid native PHP session locking
            session(['user_id' => $user->id]);

            if (App::environment(['production', 'uat'])) {
                Auth::logoutOtherDevices($email); // Logout all other sessions
            }

            $this->clearForm(); // clear all form data

            if ($user->status != config('constants.user.status.key.active')) {
                // INACTIVE user error handling
                session()->flash('error', __('messages.login.unverified_account'));

                return;
            } else {
                session()->flash('success', __('messages.login.success'));
                $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true); // redirect to user listing
            }
        } else {
            Helper::logInfo(static::class, __FUNCTION__, __('messages.login.invalid_credentials_error'), ['email' => $email]);
            session()->flash('error', __('messages.login.invalid_credentials_error'));
        }
    }

    public function render()
    {
        return view('livewire.auth.login')->title(__('messages.meta_titles.login'));
    }

    public function clearForm()
    {
        $this->email = '';
        $this->password = '';
    }
}
