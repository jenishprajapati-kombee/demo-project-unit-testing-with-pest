<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id ?? '',
            'name' => $this->name ?? '',
            'description' => $this->description ?? '',
            'code' => $this->code ?? '',
            'price' => $this->price ?? '',
            'brands' => $this->getRelationData('brands', 'brands_json', ['id', 'brand_id']),
        ];
    }

    protected function getRelationData($relation, $jsonAttr, $fields)
    {
        if ($this->relationLoaded($relation) && $this->$relation) {
            return $this->$relation->map(fn ($item) => [
                $fields[0] => $item->{$fields[0]},
                $fields[1] => $item->{$fields[1]} ?? '',
            ]);
        }
        $json = json_decode($this->getAttribute($jsonAttr) ?? $this->attributes[$jsonAttr] ?? '[]', true) ?: [];

        return collect($json)->filter(fn ($item) => ! empty($item['id']))->unique('id')->map(fn ($item) => [
            $fields[0] => $item[$fields[0]] ?? '',
            $fields[1] => $item[$fields[1]] ?? '',
        ])->values();
    }
}
