<?php

namespace App\Livewire\DeleteAccount;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Success extends Component
{
    public function render()
    {
        return view('livewire.delete-account.success')->title(__('messages.delete_account.page_title_success'));
    }
}
