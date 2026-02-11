<?php

use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use App\Models\WebUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $this->webUser = WebUser::factory()->create([
        'email' => 'web@example.com',
    ]);
});

test('component mounts with token and email', function () {
    Livewire::withQueryParams(['email' => 'test@example.com'])
        ->test(ResetPassword::class, ['token' => 'sample-token'])
        ->assertSet('token', 'sample-token')
        ->assertSet('email', 'test@example.com')
        ->assertSet('broker', 'users');
});

test('component mounts with webusers broker', function () {
    Livewire::withQueryParams(['email' => 'web@example.com', 'broker' => 'webusers'])
        ->test(ResetPassword::class, ['token' => 'sample-token'])
        ->assertSet('token', 'sample-token')
        ->assertSet('email', 'web@example.com')
        ->assertSet('broker', 'webusers');
});

test('component defaults to users broker for invalid broker param', function () {
    Livewire::withQueryParams(['broker' => 'invalid-broker'])
        ->test(ResetPassword::class, ['token' => 'sample-token'])
        ->assertSet('broker', 'users');
});

test('password can be reset successfully for default broker', function () {
    $token = Password::broker('users')->createToken($this->user);

    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('email', $this->user->email)
        ->set('password', 'Password@123')
        ->set('password_confirmation', 'Password@123')
        ->call('resetPassword')
        ->assertHasNoErrors()
        ->assertSessionHas('success', __('messages.login.reset_password_success'))
        ->assertRedirect('/');

    expect(Hash::check('Password@123', $this->user->refresh()->password))->toBeTrue();
});

test('password can be reset successfully for webusers broker', function () {
    $token = Password::broker('webusers')->createToken($this->webUser);

    Livewire::withQueryParams(['broker' => 'webusers'])
        ->test(ResetPassword::class, ['token' => $token])
        ->set('email', $this->webUser->email)
        ->set('password', 'Password@123')
        ->set('password_confirmation', 'Password@123')
        ->call('resetPassword')
        ->assertHasNoErrors()
        ->assertSessionHas('success', __('messages.login.reset_password_success'))
        ->assertRedirect('/');

    expect(Hash::check('Password@123', $this->webUser->refresh()->password))->toBeTrue();
});

test('password reset requires a token', function () {
    Livewire::test(ResetPassword::class, ['token' => ''])
        ->set('email', $this->user->email)
        ->set('password', 'Password@123')
        ->set('password_confirmation', 'Password@123')
        ->call('resetPassword')
        ->assertHasErrors(['token' => 'required']);
});

test('password reset requires an email', function () {
    Livewire::test(ResetPassword::class, ['token' => 'sample-token'])
        ->set('email', '')
        ->set('password', 'Password@123')
        ->set('password_confirmation', 'Password@123')
        ->call('resetPassword')
        ->assertHasErrors(['email' => 'required']);
});

test('password reset requires a valid email', function () {
    Livewire::test(ResetPassword::class, ['token' => 'sample-token'])
        ->set('email', 'not-an-email')
        ->set('password', 'Password@123')
        ->set('password_confirmation', 'Password@123')
        ->call('resetPassword')
        ->assertHasErrors(['email' => 'email']);
});

test('password reset requires a password', function () {
    Livewire::test(ResetPassword::class, ['token' => 'sample-token'])
        ->set('email', $this->user->email)
        ->set('password', '')
        ->set('password_confirmation', '')
        ->call('resetPassword')
        ->assertHasErrors(['password' => 'required']);
});

test('password reset requires password confirmation', function () {
    Livewire::test(ResetPassword::class, ['token' => 'sample-token'])
        ->set('email', $this->user->email)
        ->set('password', 'Password@123')
        ->set('password_confirmation', 'DifferentPassword@123')
        ->call('resetPassword')
        ->assertHasErrors(['password' => 'confirmed']);
});

test('password reset requires strong password', function () {
    Livewire::test(ResetPassword::class, ['token' => 'sample-token'])
        ->set('email', $this->user->email)
        ->set('password', 'short')
        ->set('password_confirmation', 'short')
        ->call('resetPassword')
        ->assertHasErrors(['password']);
});

test('password reset fails with invalid token', function () {
    Livewire::test(ResetPassword::class, ['token' => 'invalid-token'])
        ->set('email', $this->user->email)
        ->set('password', 'Password@123')
        ->set('password_confirmation', 'Password@123')
        ->call('resetPassword')
        ->assertSee(__('messages.login.invalid_email_error'));

    expect(Hash::check('Password@123', $this->user->refresh()->password))->toBeFalse();
});

test('password reset fails with incorrect email for token', function () {
    $token = Password::broker('users')->createToken($this->user);

    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('email', 'wrong@example.com')
        ->set('password', 'Password@123')
        ->set('password_confirmation', 'Password@123')
        ->call('resetPassword')
        ->assertSee(__('messages.login.invalid_email_error'));
});

test('token and broker properties are locked', function () {
    $component = Livewire::test(ResetPassword::class, ['token' => 'sample-token']);

    try {
        $component->set('token', 'new-token');
        $this->fail('Token property should be locked');
    } catch (\Exception $e) {
        expect($e->getMessage())->toContain('Cannot update locked property: [token]');
    }

    try {
        $component->set('broker', 'webusers');
        $this->fail('Broker property should be locked');
    } catch (\Exception $e) {
        expect($e->getMessage())->toContain('Cannot update locked property: [broker]');
    }
});
