<?php

namespace App\Traits;

use App\Helper;

trait CommonTrait
{
    public static function getOTP()
    {
        Helper::logSingleInfo(static::class, __FUNCTION__, 'Start');
        $otp = '123456';
        if (app()->isProduction()) {
            $otp = rand(100000, 999999);
        }
        Helper::logSingleInfo(static::class, __FUNCTION__, 'End');

        return $otp;
    }

    /**
     * Common Display Error Message.
     *
     * @param $query
     * @return \Illuminate\Http\JsonResponse
     */
    public static function GetError($message, $additional = [])
    {
        $response = [
            'message' => $message,
            'errors' => (object) [],
        ];

        return response()->json(array_merge($response, $additional), config('constants.validation_codes.unassigned'));
    }
}
