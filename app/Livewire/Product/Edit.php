<?php

namespace App\Livewire\Product;

use App\Livewire\Breadcrumb;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\Response;

class Edit extends Component
{
    use WithFileUploads;

    public $product;

    public $id;

    public $name;

    public $description;

    public $code;

    public $price;

    public $brands = [];

    public $brand = [];

    public function mount($id)
    {
        if (! Gate::allows('edit-product')) {
            abort(Response::HTTP_FORBIDDEN);
        }

        /* begin::Set breadcrumb */
        $segmentsData = [
            'title' => __('messages.product.breadcrumb.title'),
            'item_1' => '<a href="/product" class="text-muted text-hover-primary" wire:navigate>' . __('messages.product.breadcrumb.product') . '</a>',
            'item_2' => __('messages.product.breadcrumb.edit'),
        ];
        $this->dispatch('breadcrumbList', $segmentsData)->to(Breadcrumb::class);
        /* end::Set breadcrumb */

        $this->product = Product::find($id);

        if ($this->product) {
            foreach ($this->product->getAttributes() as $key => $value) {
                $this->{$key} = $value; // Dynamically assign the attributes to the class
            }
        } else {
            abort(Response::HTTP_NOT_FOUND);
        }

        $this->brand = \App\Models\ProductBrand::where('product_id', $this->product['id'])->pluck('brand_id');
        $this->brands = \App\Models\Brand::all();
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|max:191',
            'description' => 'required|max:500',
            'code' => 'required|max:20',
            'price' => 'required',
            'brand' => 'nullable|array',
            'brand.*' => 'required|exists:brands,id,deleted_at,NULL',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => __('messages.product.validation.messsage.name.required'),
            'name.max' => __('messages.product.validation.messsage.name.max'),
            'description.required' => __('messages.product.validation.messsage.description.required'),
            'description.max' => __('messages.product.validation.messsage.description.max'),
            'code.required' => __('messages.product.validation.messsage.code.required'),
            'code.max' => __('messages.product.validation.messsage.code.max'),
            'price.required' => __('messages.product.validation.messsage.price.required'),
            'brand.required' => __('messages.product.validation.messsage.brand.required'),
        ];
    }

    public function store()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'code' => $this->code,
            'price' => $this->price,
        ];
        $this->product->update($data); // Update data into the DB

        $this->product->brands()->detach();
        $this->product->brands()->attach($this->brand);

        session()->flash('success', __('messages.product.messages.update'));

        return $this->redirect('/product', navigate: true); // redirect to product listing page
    }

    public function render()
    {
        return view('livewire.product.edit')->title(__('messages.meta_title.edit_product'));
    }
}
