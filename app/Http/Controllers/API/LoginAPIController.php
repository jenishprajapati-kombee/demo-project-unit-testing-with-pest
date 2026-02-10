<?php

namespace App\Http\Controllers\API;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\DataTrueResource;
use App\Http\Resources\LoginResource;
use App\Models\WebUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Token;
use Throwable;

/*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the WebUser login, Change Password and logout Functionality.
    |
    */

class LoginAPIController extends Controller
{
    /**
     * Login user and create token
     *
     * @return LoginResource|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        // ========================================================================
        // STEP 1: Validate user credentials
        // ========================================================================
        $user = WebUser::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return WebUser::GetError(__('messages.login.wrong_credentials'));
        }

        // Check if user status is active
        if ($user->status !== config('constants.status.active')) {
            return WebUser::GetError(__('messages.login.account_inactive'));
        }

        // ========================================================================
        // STEP 2: Delete existing tokens and create new token
        // ========================================================================
        $user->tokens()->delete();
        $tokenResult = $user->createToken('Login Token');

        // ========================================================================
        // STEP 3: Build and return successful login response
        // ========================================================================
        // Get the token model to access its properties
        /** @var Token $tokenModel */
        $tokenModel = $tokenResult->getToken();

        // Set temporary attributes for the resource
        $user->setAttribute('authorization', $tokenResult->accessToken);
        $user->setAttribute('refresh_token', $tokenModel?->id);
        $user->setAttribute('token_expires_at', $tokenModel?->expires_at);

        // Log successful token generation in local environment
        Helper::logSingleInfo(static::class, __FUNCTION__, 'Token generated successfully', [
            'user_email' => $request->email,
        ]);

        return response()->json([
            'message' => __('messages.login.success'),
            'data' => (new LoginResource($user))->resolve(),
        ]);
    }

    /**
     * change password functionality.
     *
     * @return DataTrueResource|\Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        // get all updated data.
        $masterUser = WebUser::where('email', $request->user()->email)->first();
        $masterUser->password = Hash::make($request->new_password);

        // update user password in master user table
        if ($masterUser->save()) {
            return response()->json([
                'message' => __('messages.api.password_changed'),
                'data' => '',
            ]);
        }

        return response()->json([
            'message' => __('messages.api.something_wrong'),
            'data' => '',
        ]);
    }

    /**
     * Refresh access token using refresh token
     *
     * @return LoginResource|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function refreshingTokens(Request $request)
    {
        try {
            // ========================================================================
            // STEP 1: Validate refresh token
            // ========================================================================
            $request->validate([
                'refresh_token' => 'required',
            ]);

            // ========================================================================
            // STEP 2: Find token by ID and get user
            // ========================================================================
            $token = Token::where('id', $request->refresh_token)
                ->where('revoked', false)
                ->first();

            if (!$token) {
                return WebUser::GetError(__('messages.api.login.invalid_refresh_token'));
            }

            $user = WebUser::find($token->user_id);

            if (!$user) {
                Helper::logSingleInfo(static::class, __FUNCTION__, 'User not found for access token during refresh.', [
                    'user_id' => $token->user_id,
                    'token_id' => $request->refresh_token,
                ]);

                return WebUser::GetError(__('messages.api.login.user_not_found'));
            }

            // Check if user status is active
            if ($user->status !== config('constants.status.active')) {
                return WebUser::GetError(__('messages.login.account_inactive'));
            }

            // ========================================================================
            // STEP 3: Delete existing tokens and create new token
            // ========================================================================
            $user->tokens()->delete();
            $tokenResult = $user->createToken('Login Token');

            // ========================================================================
            // STEP 4: Build and return successful refresh response
            // ========================================================================
            // Get the token model to access its properties
            /** @var Token $tokenModel */
            $tokenModel = $tokenResult->getToken();

            // Store token data in variables
            $authorization = $tokenResult->accessToken;
            $refreshToken = $tokenModel?->id;
            $tokenExpiresAt = $tokenModel?->expires_at;

            // Set temporary attributes for the resource (if needed elsewhere)
            $user->setAttribute('authorization', $authorization);
            $user->setAttribute('refresh_token', $refreshToken);
            $user->setAttribute('token_expires_at', $tokenExpiresAt);

            // Log successful token refresh in local environment
            Helper::logSingleInfo(static::class, __FUNCTION__, 'Token refreshed successfully', [
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]);

            return response()->json([
                'authorization' => $authorization,
                'refresh_token' => $refreshToken,
                'token_expires_at' => $tokenExpiresAt ? (is_string($tokenExpiresAt) ? \Carbon\Carbon::parse($tokenExpiresAt)->format(config('constants.api_datetime_format')) : $tokenExpiresAt->format(config('constants.api_datetime_format'))) : '',
            ]);
        } catch (Throwable $th) {
            Helper::logCatchError($th, static::class, __FUNCTION__);

            return WebUser::GetError(__('messages.api.list_fail'));
        }
    }

    /**
     * Logout User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function logout(Request $request)
    {
        $token = $request->user()->token();
        if ($token && method_exists($token, 'revoke')) {
            $token->revoke();
        }

        return response()->json(__('messages.login.logout'));
    }
}
