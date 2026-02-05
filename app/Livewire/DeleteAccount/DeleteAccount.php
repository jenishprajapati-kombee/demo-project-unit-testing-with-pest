<?php

namespace App\Livewire\DeleteAccount;

use App\Helper;
use App\Services\DeleteAccountService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;

#[Layout('components.layouts.auth')]
class DeleteAccount extends Component
{
    public function mount(DeleteAccountService $deleteAccountService)
    {
        try {
            Helper::logInfo(static::class, __FUNCTION__, 'Start');

            $accessToken = request()->query('access_token');

            if (! $accessToken) {
                Helper::logSingleError(static::class, __FUNCTION__, 'Access token is missing in request.');

                return redirect()->route('delete-account.remove');
            }

            $verifyToken = $deleteAccountService->verifyAccessToken($accessToken);

            if (is_null($verifyToken)) {
                Helper::logSingleError(static::class, __FUNCTION__, 'Verify token did not rectify.');

                return redirect()->route('delete-account.remove');
            }

            Helper::logInfo(static::class, __FUNCTION__, 'End');

            return redirect()->route('delete-account.readDatasecurity', ['access_token' => $accessToken]);
        } catch (Throwable $th) {
            Helper::logCatchError($th, static::class, __FUNCTION__);

            return redirect()->route('delete-account.remove');
        }
    }

    public function render()
    {
        return view('livewire.delete-account.delete-account');
    }
}
