<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\ForgotPassword;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

beforeEach(function () {
    // Clear rate limits before each test to ensure isolation
    RateLimiter::clear('ip_restrication127.0.0.1');
});

it('renders the forgot password page', function () {
    Livewire::test(ForgotPassword::class)
        ->assertStatus(200)
        ->assertSee(__('messages.login.forgot_password_title'));
});

it('can send a password reset link with valid email', function () {
    User::factory()->create(['email' => 'test@example.com']);

    Password::shouldReceive('broker')->andReturn(
        $broker = \Mockery::mock(\Illuminate\Contracts\Auth\PasswordBroker::class)
    );
    $broker->shouldReceive('sendResetLink')->once()->andReturn(Password::RESET_LINK_SENT);

    Livewire::test(ForgotPassword::class)
        ->set('email', 'test@example.com')
        ->call('sendPasswordResetLink')
        ->assertHasNoErrors()
        ->assertRedirect('/')
        ->assertSessionHas('success', __('messages.login.forgot_password_success'));
});

it('fails to send reset link with invalid email format', function () {
    Livewire::test(ForgotPassword::class)
        ->set('email', 'invalid-email')
        ->call('sendPasswordResetLink')
        ->assertHasErrors(['email' => 'email']);
});

it('fails to send reset link to non-existent email', function () {
    Password::shouldReceive('broker')->andReturn(
        $broker = \Mockery::mock(\Illuminate\Contracts\Auth\PasswordBroker::class)
    );
    $broker->shouldReceive('sendResetLink')->once()->andReturn(Password::INVALID_USER);

    Livewire::test(ForgotPassword::class)
        ->set('email', 'nonexistent@example.com')
        ->call('sendPasswordResetLink')
        ->assertSee(__('messages.login.invalid_email_error'));
});

it('respects IP rate limiting', function () {
    $ip = '127.0.0.1';
    $limit = config('constants.rate_limiting.limit.ip_attempt_limit');

    for ($i = 0; $i < $limit; $i++) {
        RateLimiter::hit('ip_restrication' . $ip, 86400);
    }

    Livewire::test(ForgotPassword::class)
        ->set('email', 'test@example.com')
        ->call('sendPasswordResetLink')
        ->assertSee(__('messages.login.ratelimit_ip_restrication'));
});

it('respects Email rate limiting', function () {
    $email = 'email-limited@example.com';
    $limit = config('constants.rate_limiting.limit.email_attempt_limit');

    RateLimiter::clear('email_restrication' . $email);

    for ($i = 0; $i < $limit; $i++) {
        RateLimiter::hit('email_restrication' . $email, 86400);
    }

    Livewire::test(ForgotPassword::class)
        ->set('email', $email)
        ->call('sendPasswordResetLink')
        ->assertSee(__('messages.login.ratelimit_email_restrication'));
});

it('respects Component rate limiting', function () {
    $email = 'component-limited@example.com';

    RateLimiter::clear('FGT' . $email);

    Livewire::test(ForgotPassword::class)
        ->set('email', $email)
        ->call('sendPasswordResetLink');

    Livewire::test(ForgotPassword::class)
        ->set('email', $email)
        ->call('sendPasswordResetLink')
        ->assertSee(__('messages.login.ratelimit_forgot_password'))
        ->assertSet('email', '');
});

it('normalizes email by removing spaces', function () {
    Password::shouldReceive('broker')->andReturn(
        $broker = \Mockery::mock(\Illuminate\Contracts\Auth\PasswordBroker::class)
    );
    $broker->shouldReceive('sendResetLink')->andReturn(Password::RESET_LINK_SENT);

    Livewire::test(ForgotPassword::class)
        ->set('email', ' test @ example . com ')
        ->call('sendPasswordResetLink')
        ->assertSet('email', 'test@example.com');
});
