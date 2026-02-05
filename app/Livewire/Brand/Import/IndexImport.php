<?php

namespace App\Livewire\Brand\Import;

use App\Livewire\Breadcrumb;
use Livewire\Component;

class IndexImport extends Component
{
    public $importData;

    public function mount()
    {
        /* Set breadcrumb */
        $segmentsData = [
            'title' => __('messages.import_history.breadcrumb.title'),
            'item_1' => __('messages.import_history.breadcrumb.brand'),
            'item_2' => __('messages.import_history.breadcrumb.list'),
        ];
        $this->dispatch('breadcrumbList', $segmentsData)->to(Breadcrumb::class);

        $this->importData = [
            'folderName' => config('constants.import_csv_log.folder_name.new.brand'),
            'modelName' => config('constants.import_csv_log.models.brand'),
        ];
    }

    public function render()
    {
        return view('livewire.brand.import.index-import')->title(__('messages.meta_title.index_brand'));
    }
}
