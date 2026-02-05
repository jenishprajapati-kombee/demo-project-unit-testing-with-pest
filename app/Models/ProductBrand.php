<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBrand extends Model
{
    protected $table = 'product_brand';

    protected $fillable = ['product_id', 'brand_id'];

    public $timestamps = false;
}
