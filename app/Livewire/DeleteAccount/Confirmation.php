<?php

namespace App\Livewire\DeleteAccount;

use App\Helper;
use App\Services\DeleteAccountService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Confirmation extends Component
{
    public ?string $accessToken = null;

    public $errorMessage = '';

    public function mount($access_token = null)
    {
        $this->accessToken = $access_token ?? request()->query('access_token') ?? session()->get('access_token');

        if (! $this->accessToken) {
            return redirect()->route('delete-account.remove');
        }

        // Check if data security was read
        $readDatasecurity = session()->get('readDatasecurity');
        if (! $readDatasecurity) {
            return redirect()->route('delete-account.readDatasecurity', ['access_token' => $this->accessToken]);
        }

        session()->put('access_token', $this->accessToken);
    }

    public function confirmDelete(DeleteAccountService $deleteAccountService)
    {
        $this->resetErrorBag();
        $this->errorMessage = '';

        // Verify access token
        $token = $deleteAccountService->verifyAccessToken($this->accessToken);

        if (! $token) {
            $this->errorMessage = __('messages.app_login.refresh_token_invalid');
            Helper::logInfo(static::class, __FUNCTION__, 'Access token did not verify.');

            return;
        }

        // Delete account
        $result = $deleteAccountService->deleteAccount($this->accessToken);

        if (! $result['success']) {
            $this->errorMessage = $result['message'];
            Helper::logInfo(static::class, __FUNCTION__, 'Account deletion failed.');

            return;
        }

        // Clear session
        session()->forget('access_token');
        session()->forget('readDatasecurity');

        // Redirect to success page
        $this->redirect(route('delete-account.success'), navigate: true);
    }

    public function cancel()
    {
        // Clear session and redirect to home or login
        session()->forget('access_token');
        session()->forget('readDatasecurity');

        // Redirect back to remove account page
        $this->redirect(route('delete-account.remove'), navigate: true);
    }

    public function render()
    {
        return view('livewire.delete-account.confirmation')->title(__('messages.delete_account.page_title_confirmation'));
    }
}
