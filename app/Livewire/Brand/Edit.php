<?php

namespace App\Livewire\Brand;

use App\Helper;
use App\Livewire\Breadcrumb;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\Response;

class Edit extends Component
{
    use WithFileUploads;

    public $brand;

    public $id;

    public $name;

    public $remark;

    public $bob;

    public $description;

    public $country_id;

    public $countries = [];

    public $state_id;

    public $states = [];

    public $city_id;

    public $cities = [];

    public $status;

    public function mount($id)
    {
        if (! Gate::allows('edit-brand')) {
            abort(Response::HTTP_FORBIDDEN);
        }

        /* begin::Set breadcrumb */
        $segmentsData = [
            'title' => __('messages.brand.breadcrumb.title'),
            'item_1' => '<a href="/brand" class="text-muted text-hover-primary" wire:navigate>' . __('messages.brand.breadcrumb.brand') . '</a>',
            'item_2' => __('messages.brand.breadcrumb.edit'),
        ];
        $this->dispatch('breadcrumbList', $segmentsData)->to(Breadcrumb::class);
        /* end::Set breadcrumb */

        $this->brand = Brand::find($id);

        if ($this->brand) {
            foreach ($this->brand->getAttributes() as $key => $value) {
                $this->{$key} = $value; // Dynamically assign the attributes to the class
            }
        } else {
            abort(Response::HTTP_NOT_FOUND);
        }

        $this->countries = Helper::getAllCountry();
        $this->states = Helper::getAllState();
        $this->cities = Helper::getAllCity();
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|max:191',
            'remark' => 'nullable',
            'bob' => 'required|date_format:Y-m-d H:i:s',
            'description' => 'required|max:500',
            'country_id' => 'required|exists:countries,id,deleted_at,NULL',
            'state_id' => 'required|exists:states,id,deleted_at,NULL',
            'city_id' => 'required|exists:cities,id,deleted_at,NULL',
            'status' => 'required|in:Y,N',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => __('messages.brand.validation.messsage.name.required'),
            'name.max' => __('messages.brand.validation.messsage.name.max'),
            'bob.required' => __('messages.brand.validation.messsage.bob.required'),
            'bob.date_format' => __('messages.brand.validation.messsage.bob.date_format'),
            'description.required' => __('messages.brand.validation.messsage.description.required'),
            'description.max' => __('messages.brand.validation.messsage.description.max'),
            'country_id.required' => __('messages.brand.validation.messsage.country_id.required'),
            'state_id.required' => __('messages.brand.validation.messsage.state_id.required'),
            'city_id.required' => __('messages.brand.validation.messsage.city_id.required'),
            'status.required' => __('messages.brand.validation.messsage.status.required'),
            'status.in' => __('messages.brand.validation.messsage.status.in'),
        ];
    }

    public function store()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'remark' => $this->remark,
            'bob' => $this->bob,
            'description' => $this->description,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'status' => $this->status,
        ];
        $this->brand->update($data); // Update data into the DB

        session()->flash('success', __('messages.brand.messages.update'));

        return $this->redirect('/brand', navigate: true); // redirect to brand listing page
    }

    public function render()
    {
        return view('livewire.brand.edit')->title(__('messages.meta_title.edit_brand'));
    }
}
