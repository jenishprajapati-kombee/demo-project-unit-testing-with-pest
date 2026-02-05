<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    public $id;

    public $product;

    public $event = 'showproductInfoModal';

    #[On('show-product-info')]
    public function show($id)
    {
        $this->product = null;

        $this->product = Product::select(
            'products.id',
            'products.name',
            'products.description',
            'products.code',
            'products.price',
            DB::raw('GROUP_CONCAT(DISTINCT brands.name) AS brands_name')
        )
            ->leftJoin('product_brand', 'product_brand.product_id', '=', 'products.id')
            ->leftJoin('brands', 'brands.id', '=', 'product_brand.brand_id')
            ->where('products.id', $id)
            ->groupBy('products.id')
            ->first();

        if (! is_null($this->product)) {
            $this->dispatch('show-modal', id: '#' . $this->event);
        } else {
            session()->flash('error', __('messages.product.messages.record_not_found'));
        }
    }

    public function render()
    {
        return view('livewire.product.show');
    }
}
