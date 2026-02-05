<?php

namespace App\Livewire\DeleteAccount;

use App\Helper;
use App\Models\WebUser;
use App\Services\DeleteAccountService;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class VerifyOtp extends Component
{
    public ?string $email = null;

    public string $password = '';

    public $errorMessage = '';

    public function mount($email = null)
    {
        $this->email = $email ?? request()->query('email');

        if (! $this->email) {
            $this->redirect(route('delete-account.remove'), navigate: true);

            return;
        }

        // Verify email exists
        $user = WebUser::where('email', Str::lower(trim($this->email)))->first();
        if (! $user) {
            $this->errorMessage = __('messages.app_login.account_not_exist');
            $this->redirect(route('delete-account.remove'), navigate: true);

            return;
        }

        // Verify login_type is email password
        $loginType = $user->login_type ?? config('constants.login_type.key.email_password');
        if ($loginType != config('constants.login_type.key.email_password')) {
            $this->errorMessage = __('messages.app_login.invalid_login_type');
            $this->redirect(route('delete-account.remove'), navigate: true);

            return;
        }
    }

    public function verifyPassword(DeleteAccountService $deleteAccountService)
    {
        $this->validate(['password' => 'required|string|min:6|max:191']);

        $this->resetErrorBag();
        $this->errorMessage = '';

        $email = Str::lower(trim($this->email));

        // Use service to verify password
        $result = $deleteAccountService->verifyPassword($email, $this->password);

        if (! $result['success']) {
            $this->errorMessage = $result['message'];
            Helper::logInfo(static::class, __FUNCTION__, 'Password verification failed.', ['email' => $email]);

            return;
        }

        // Store access token in session
        session(['access_token' => $result['data']['access_token']]);

        // Redirect to data security page
        $this->redirect(route('delete-account.readDatasecurity'), navigate: true);
    }

    public function render()
    {
        return view('livewire.delete-account.verify-otp')->title(__('messages.delete_account.page_title_verify_password'));
    }
}
