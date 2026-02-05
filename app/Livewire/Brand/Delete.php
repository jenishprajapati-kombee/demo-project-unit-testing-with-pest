<?php

namespace App\Livewire\Brand;

use App\Models\Brand;
use Livewire\Attributes\On;
use Livewire\Component;

class Delete extends Component
{
    public $selectedBrandIds = [];

    public $tableName;

    public bool $showModal = false;

    public bool $isBulkDelete = false;

    public int $selectedBrandCount = 0;

    public string $userName = '';

    public $message;

    #[On('delete-confirmation')]
    public function deleteConfirmation($ids, $tableName)
    {
        $this->handleDeleteConfirmation($ids, $tableName);
    }

    #[On('bulk-delete-confirmation')]
    public function bulkDeleteConfirmation($data)
    {
        $ids = $data['ids'] ?? [];
        $tableName = $data['tableName'] ?? '';
        $this->handleDeleteConfirmation($ids, $tableName);
    }

    #[On('delete-confirmation')]
    public function handleDeleteConfirmation($ids, $tableName)
    {
        // Initialize table name and reset selected ids
        $this->tableName = $tableName;
        $this->selectedBrandIds = [];

        // Fetch the ids of the roles that match the given IDs and organization ID
        $brandIds = Brand::whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (! empty($brandIds)) {
            $this->selectedBrandIds = $ids;

            $this->selectedBrandCount = count($this->selectedBrandIds);
            $this->isBulkDelete = $this->selectedBrandCount > 1;

            // Get user name for single delete
            if (! $this->isBulkDelete) {
                $this->message = __('messages.brand.messages.delete_confirmation_text');
            } else {
                $this->message = __('messages.brand.messages.bulk_delete_confirmation_text', ['count' => count($this->selectedBrandIds)]);
            }

            $this->showModal = true;
        } else {
            // If no roles were found, show an error message
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => __('messages.brand.delete.record_not_found'),
            ]);
        }
    }

    public function confirmDelete()
    {
        if (! empty($this->selectedBrandIds)) {
            // Proceed with deletion of selected brand
            Brand::whereIn('id', $this->selectedBrandIds)->delete();

            session()->flash('success', __('messages.brand.messages.delete'));

            return $this->redirect(route('brand.index'), navigate: true);
        } else {
            $this->dispatch('alert', type: 'error', message: __('messages.user.messages.record_not_found'));
        }
    }

    public function hideModal()
    {
        $this->showModal = false;
        $this->selectedBrandIds = [];
        $this->selectedBrandCount = 0;
        $this->isBulkDelete = false;
        $this->userName = '';
    }

    public function render()
    {
        return view('livewire.brand.delete');
    }
}
