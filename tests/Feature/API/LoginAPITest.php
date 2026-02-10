<?php

namespace Tests\Feature\API;

use App\Models\WebUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Laravel\Passport\Token;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Login API Tests
|--------------------------------------------------------------------------
*/

beforeEach(function () {
    $this->artisan('passport:client', ['--personal' => true, '--name' => 'Test Personal Access Client', '--provider' => 'webusers', '--no-interaction' => true]);
});

it('can login with valid credentials', function () {
    $password = 'password123';
    $user = WebUser::factory()->create([
        'email' => 'api-test@example.com',
        'password' => Hash::make($password),
        'status' => config('constants.status.active'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => $user->email,
        'password' => $password,
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'name',
                'email',
                'authorization',
                'refresh_token',
                'token_expires_at',
            ],
        ])
        ->assertJsonPath('message', __('messages.login.success'));

    $this->assertCount(1, $user->tokens);
});

it('fails to login with wrong credentials', function () {
    $user = WebUser::factory()->create([
        'email' => 'api-test@example.com',
        'password' => Hash::make('password123'),
        'status' => config('constants.status.active'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(config('constants.validation_codes.unassigned'))
        ->assertJsonPath('message', __('messages.login.wrong_credentials'));
});

it('fails to login with inactive account', function () {
    $password = 'password123';
    $user = WebUser::factory()->create([
        'email' => 'inactive-api@example.com',
        'password' => Hash::make($password),
        'status' => config('constants.status.inactive'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => $user->email,
        'password' => $password,
    ]);

    $response->assertStatus(config('constants.validation_codes.unassigned'))
        ->assertJsonPath('message', __('messages.login.account_inactive'));
});

it('validates login request fields', function () {
    $response = $this->postJson('/api/v1/login', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});

/*
|--------------------------------------------------------------------------
| Refresh Token API Tests
|--------------------------------------------------------------------------
*/

it('can refresh tokens with valid refresh token', function () {
    $user = WebUser::factory()->create([
        'status' => config('constants.status.active'),
    ]);

    // Create a token and get it manually since we need the ID as refresh_token
    $tokenResult = $user->createToken('Test Token');
    $tokenId = $tokenResult->token->id;

    $response = $this->postJson('/api/v1/refreshing-tokens', [
        'refresh_token' => $tokenId,
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'authorization',
            'refresh_token',
            'token_expires_at',
        ]);

    // Old tokens should be deleted
    $this->assertCount(1, $user->tokens);
    $this->assertDatabaseMissing('oauth_access_tokens', ['id' => $tokenId]);
});

it('fails to refresh with invalid refresh token', function () {
    $response = $this->postJson('/api/v1/refreshing-tokens', [
        'refresh_token' => 'non-existent-token-id',
    ]);

    $response->assertStatus(config('constants.validation_codes.unassigned'))
        ->assertJsonPath('message', __('messages.api.login.invalid_refresh_token'));
});

it('fails to refresh if user is inactive', function () {
    $user = WebUser::factory()->create([
        'status' => config('constants.status.inactive'),
    ]);

    $tokenResult = $user->createToken('Test Token');
    $tokenId = $tokenResult->token->id;

    $response = $this->postJson('/api/v1/refreshing-tokens', [
        'refresh_token' => $tokenId,
    ]);

    $response->assertStatus(config('constants.validation_codes.unassigned'))
        ->assertJsonPath('message', __('messages.login.account_inactive'));
});

/*
|--------------------------------------------------------------------------
| Change Password API Tests
|--------------------------------------------------------------------------
*/

it('can change password with valid data', function () {
    $oldPassword = 'password123';
    $newPassword = 'newpassword123';
    $user = WebUser::factory()->create([
        'password' => Hash::make($oldPassword),
    ]);

    Passport::actingAs($user, [], 'api');

    $response = $this->postJson('/api/v1/change-password', [
        'old_password' => $oldPassword,
        'new_password' => $newPassword,
        'confirm_password' => $newPassword,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('message', __('messages.api.password_changed'));

    $user->refresh();
    $this->assertTrue(Hash::check($newPassword, $user->password));
});

it('fails to change password with incorrect old password', function () {
    $user = WebUser::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    Passport::actingAs($user, [], 'api');

    $response = $this->postJson('/api/v1/change-password', [
        'old_password' => 'wrong-password',
        'new_password' => 'newpassword123',
        'confirm_password' => 'newpassword123',
    ]);

    // Validation fails in ChangePasswordRequest
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['old_password']);
});

it('validates change password request rules', function () {
    $user = WebUser::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    Passport::actingAs($user, [], 'api');

    // Test password same as old
    $response = $this->postJson('/api/v1/change-password', [
        'old_password' => 'password123',
        'new_password' => 'password123',
        'confirm_password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['new_password']);
});

/*
|--------------------------------------------------------------------------
| Logout API Tests
|--------------------------------------------------------------------------
*/

it('can logout successfully', function () {
    $user = WebUser::factory()->create();
    Passport::actingAs($user, [], 'api');

    $response = $this->postJson('/api/v1/logout');

    $response->assertStatus(200)
        ->assertJsonPath(null, __('messages.login.logout'));
});

it('requires authentication for logout', function () {
    $response = $this->postJson('/api/v1/logout');

    $response->assertStatus(401);
});
