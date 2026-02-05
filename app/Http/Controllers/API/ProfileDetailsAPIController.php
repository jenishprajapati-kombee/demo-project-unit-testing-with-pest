<?php

namespace App\Http\Controllers\API;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\WebUserUpdateRequest;
use App\Http\Resources\WebUserResource;
use App\Models\WebUser;
use App\Traits\UploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

/*
    |--------------------------------------------------------------------------
    | Profile Details API Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the WebUser profile details retrieval and update functionality.
    |
    */

class ProfileDetailsAPIController extends Controller
{
    use UploadTrait;

    public function getProfileDetails(Request $request)
    {
        try {
            $user = $request->user();

            if (! $user) {
                return WebUser::GetError(__('messages.common_error_message'));
            }

            $profileDetails = WebUser::find($user->id);

            if (! $profileDetails) {
                return WebUser::GetError(__('messages.record_not_found'));
            }

            return response()->json([
                'message' => __('messages.profile.profile_retrieved_successfully'),
                'data' => (new WebUserResource($profileDetails->load([])))->resolve(),
            ]);
        } catch (Throwable $th) {
            // Log error
            Helper::logCatchError($th, static::class, __FUNCTION__, [], $request->user());

            return WebUser::GetError(__('messages.common_error_message'));
        }
    }

    public function updateProfileDetails(WebUserUpdateRequest $request)
    {
        try {
            $user = $request->user();

            if (! $user) {
                return WebUser::GetError(__('messages.common_error_message'));
            }

            $profileDetails = WebUser::find($user->id);

            if (! $profileDetails) {
                return WebUser::GetError(__('messages.record_not_found'));
            }

            $data = $request->all();

            // Handle file uploads
            // Handle profile image upload if provided
            if ($request->hasFile('profile')) {
                // Delete old file if exists
                if ($profileDetails->profile && Storage::exists($profileDetails->profile)) {
                    Storage::delete($profileDetails->profile);
                }
                $realPath = 'web_user/' . $profileDetails->id . '/profile/';
                $resizeImages = self::resizeImages($request->file('profile'), $realPath, false, false);
                $data['profile'] = $resizeImages['image'];
            }

            $profileDetails->fill($data);
            $profileDetails->save();

            return response()->json([
                'message' => __('messages.profile.profile_updated_successfully'),
                'data' => (new WebUserResource($profileDetails))->resolve(),
            ]);
        } catch (Throwable $th) {
            // Log error
            Helper::logCatchError($th, static::class, __FUNCTION__, [], $request->user());

            return WebUser::GetError(__('messages.api.user.something_went_wrong'));
        }
    }

    public function updateProfileImage(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return WebUser::GetError(__('messages.common_error_message'));
        }

        $profileDetails = WebUser::find($user->id);

        if (! $profileDetails) {
            return WebUser::GetError(__('messages.record_not_found'));
        }

        // Validate image upload
        $request->validate([
            'profile' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
        ]);

        if (! $request->hasFile('profile')) {
            return WebUser::GetError(__('messages.profile.image_required'));
        }

        // Handle file upload
        $data = [];
        // Delete old file if exists
        if ($profileDetails->profile && Storage::exists($profileDetails->profile)) {
            Storage::delete($profileDetails->profile);
        }
        $realPath = 'web_user/' . $profileDetails->id . '/profile/';
        $resizeImages = self::resizeImages($request->file('profile'), $realPath, false, false);
        $data['profile'] = $resizeImages['image'];

        // Update profile with uploaded file path
        $profileDetails->fill($data);
        $profileDetails->save();

        return response()->json([
            'message' => __('messages.profile_updated_successfully'),
            'data' => (new WebUserResource($profileDetails))->resolve(),
        ]);
    }
}
