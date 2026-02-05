<?php

namespace App\Livewire\Role;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public $role;

    public $id;

    public $name;

    public $status;

    public function mount($id)
    {
        $this->role = Role::find($id);

        if ($this->role) {
            foreach ($this->role->getAttributes() as $key => $value) {
                $this->{$key} = $value; // Dynamically assign the attributes to the class
            }
        }
    }

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
        $this->role->update($data); // Update data into the DB

        session()->flash('success', __('messages.role.messages.update'));

        return $this->redirect('/role', navigate: true); // redirect to role listing page
    }

    public function render()
    {
        return view('livewire.role.edit');
    }
}
