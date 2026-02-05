<?php

namespace App\Services;

use App\Helper;
use App\Models\WebUser;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Token;
use Throwable;

class DeleteAccountService
{
    /**
     * Verify password and generate access token
     */
    public function verifyPassword(string $email, string $password): array
    {
        Helper::logInfo(static::class, __FUNCTION__, 'Start', ['email' => $email]);

        $user = WebUser::where('email', strtolower(trim($email)))->first();

        if (! $user) {
            return [
                'success' => false,
                'message' => __('messages.app_login.account_not_exist'),
            ];
        }

        // Verify password
        if (! Hash::check($password, $user->password)) {
            Helper::logSingleError(static::class, __FUNCTION__, 'Invalid password.', [
                'email' => $email,
            ]);

            return [
                'success' => false,
                'message' => __('messages.login.wrong_credentials'),
            ];
        }

        // Password verified, generate access token
        if (App::environment(['production'])) {
            $user->tokens()->delete(); // Revoke existing tokens
        }

        $tokenResult = $user->createToken('Delete Account Token');
        $accessToken = $tokenResult->accessToken;

        Helper::logInfo(static::class, __FUNCTION__, 'End');

        return [
            'success' => true,
            'message' => __('messages.delete_account.password_verified'),
            'data' => [
                'access_token' => base64_encode($accessToken),
            ],
        ];
    }

    /**
     * Verify access token
     */
    public function verifyAccessToken(string $accessToken): ?Token
    {
        try {
            Helper::logSingleInfo(static::class, __FUNCTION__, 'Start');
            $accessTkn = base64_decode($accessToken);

            // JWT tokens have 3 parts separated by dots: header.payload.signature
            $parts = explode('.', $accessTkn);
            if (count($parts) !== 3) {
                return null;
            }

            // Decode the payload (second part)
            $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);

            if (! isset($payload['jti'])) {
                return null;
            }

            $tokenId = $payload['jti'];
            Helper::logSingleInfo(static::class, __FUNCTION__, 'End', ['tokenId' => $tokenId]);

            return Token::where('id', $tokenId)->where('revoked', 0)->first();
        } catch (Throwable $th) {
            Helper::logCatchError($th, static::class, __FUNCTION__, ['access_token' => $accessToken]);

            return null;
        }
    }

    /**
     * Delete user account
     */
    public function deleteAccount(string $accessToken): array
    {
        Helper::logInfo(static::class, __FUNCTION__, 'Start');

        $token = $this->verifyAccessToken($accessToken);

        if (! $token) {
            return [
                'success' => false,
                'message' => __('messages.app_login.refresh_token_invalid'),
            ];
        }

        $user = WebUser::find($token->user_id);

        if (! $user) {
            return [
                'success' => false,
                'message' => __('messages.app_login.account_not_exist'),
            ];
        }

        // Revoke all tokens
        $user->tokens()->delete();

        // Soft delete user
        $user->delete();

        Helper::logInfo(static::class, __FUNCTION__, 'End', ['user_id' => $user->id]);

        return [
            'success' => true,
            'message' => __('messages.app_profile_details.delete_profile_success'),
        ];
    }
}
