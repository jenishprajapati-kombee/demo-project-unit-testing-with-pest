<?php

namespace App\Livewire\DeleteAccount;

use App\Helper;
use App\Services\DeleteAccountService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class ReadDatasecurity extends Component
{
    public ?string $accessToken = null;

    public $errorMessage = '';

    public function mount($access_token = null)
    {
        $this->accessToken = $access_token ?? request()->query('access_token') ?? session()->get('access_token');

        if (! $this->accessToken) {
            return redirect()->route('delete-account.remove');
        }

        session()->put('access_token', $this->accessToken);
    }

    public function understand(DeleteAccountService $deleteAccountService)
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

        // Store in session that user has read data security
        session()->put('readDatasecurity', 1);

        // Redirect to confirmation page
        $this->redirect(route('delete-account.confirmation'), navigate: true);
    }

    public function render()
    {
        return view('livewire.delete-account.read-datasecurity')->title(__('messages.delete_account.page_title_data_security'));
    }
}
