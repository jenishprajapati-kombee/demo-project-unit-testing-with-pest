<?php

namespace App\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public function mount()
    {
        /* begin::Set breadcrumb */
        $segmentsData = [
            'title' => __('messages.dashboard.breadcrumb.title'),
            'item_1' => __('messages.dashboard.breadcrumb.sub_title'),
            'item_2' => __('messages.dashboard.breadcrumb.list'),
        ];
        $this->dispatch('breadcrumbList', $segmentsData)->to(Breadcrumb::class);
        /* end::Set breadcrumb */
    }

    public function render()
    {
        return view('livewire.dashboard')->title(__('messages.meta_titles.dashboard'));
    }
}
