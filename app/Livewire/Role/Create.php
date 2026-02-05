<?php

namespace App\Livewire\Role;

use App\Models\Role;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $id;

    public $name;

    public $status;

    public function mount() {}

    public function rules()
    {
        $rules = [
            'name' => 'required|max:191',
            'status' => 'required|in:Y,N',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => __('messages.role.validation.messsage.name.required'),
            'name.max' => __('messages.role.validation.messsage.name.max'),
            'status.required' => __('messages.role.validation.messsage.status.required'),
            'status.in' => __('messages.role.validation.messsage.status.in'),
        ];
    }

    public function store()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'status' => $this->status,
        ];
        $role = Role::create($data);

        session()->flash('success', __('messages.role.messages.success'));

        return $this->redirect('/role', navigate: true); // redirect to role listing page
    }

    public function render()
    {
        return view('livewire.role.create');
    }
}
