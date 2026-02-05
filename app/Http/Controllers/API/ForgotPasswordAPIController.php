<?php

namespace App\Http\Controllers\API;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Models\WebUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/*
   |--------------------------------------------------------------------------
   | Password Reset Controller
   |--------------------------------------------------------------------------
   |
   | This controller is responsible for handling password reset emails and
   | includes a trait which assists in sending these notifications from
   | your application to your users. Feel free to explore this trait.
   |
   */

class ForgotPasswordAPIController extends Controller
{
    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        $email = str_replace(' ', '', $request->email);

        // Rate Limitor For Resend Otp 5 times PerMinute
        $ipPerDay = RateLimiter::tooManyAttempts('ip_restrication' . $request->ip(), config('constants.rate_limiting.limit.ip_attempt_limit'));
        if ($ipPerDay == true) {
            Helper::logError(static::class, __FUNCTION__, __('messages.login.ratelimit_ip_restrication'), ['email' => $email, 'ip' => $request->ip()]);

            return WebUser::GetError(__('messages.login.ratelimit_ip_restrication'));
        }

        $mailPerDay = RateLimiter::tooManyAttempts('email_restrication' . $email, config('constants.rate_limiting.limit.email_attempt_limit'));
        if ($mailPerDay == true) {
            Helper::logError(static::class, __FUNCTION__, __('messages.login.ratelimit_email_restrication'), ['email' => $email]);

            return WebUser::GetError(__('messages.login.ratelimit_email_restrication'));
        }

        $executed = RateLimiter::attempt(
            'FGT' . $email,
            $perMinute = 1,
            function () {
                // Send message...
            },
            config('constants.rate_limiting.limit.forgot_password')
        );

        if (! $executed) {
            Helper::logError(static::class, __FUNCTION__, __('messages.login.ratelimit_forgot_password'), ['email' => $email]);

            return WebUser::GetError(__('messages.login.ratelimit_forgot_password'));
        }

        $user = WebUser::where('email', $email)->first();

        if (! $user) {
            return WebUser::GetError(__('messages.login.invalid_email_error'));
        }

        if ($user->status !== config('constants.status.active')) {
            return WebUser::GetError(__('messages.login.account_inactive'));
        }

        $response = Password::broker('webusers')->sendResetLink(
            ['email' => $email]
        );

        if ($response == Password::RESET_LINK_SENT) {
            // Increment the rate limiter attempts
            RateLimiter::hit('ip_restrication' . $request->ip(), config('constants.rate_limiting.limit.one_day'));
            RateLimiter::hit('email_restrication' . $email, config('constants.rate_limiting.limit.one_day'));

            return $this->sendResetLinkResponse($request, $response);
        } else {
            return $this->sendResetLinkFailedResponse($request, $response);
        }
    }

    protected function sendResetLinkResponse(Request $request, $response)
    {
        return response()->json(['message' => __('messages.api.forgotpassword_success'), 'data' => ''], config('constants.validation_codes.ok'));
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'email' => [trans($response)],
            ]);
        }

        return WebUser::GetError(trans($response));
    }
}
