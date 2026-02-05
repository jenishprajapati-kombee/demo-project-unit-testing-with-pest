<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;

use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\DataTrueResource;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/*
   |--------------------------------------------------------------------------
   | products Controller
   |--------------------------------------------------------------------------
   |
   | This controller handles the products of
     index,
     show,
     store,
     update,
     destroy,
     deleteAll,
     export and
     importBulk Methods.
   |
   */

class ProductAPIController extends Controller
{
    /**
     * list products
     *
     * @return ProductCollection
     */
    public function index(Request $request)
    {
        if ($request->get('is_light', false)) {
            return Cache::rememberForever('product.all', function () {
                $product = new Product();
                /** @var array<string> $lightFields */
                $lightFields = $product->light ?? [];
                $query = Product::select($lightFields);
                $query->with(['brands']);
                $query = $query->get();

                return new ProductCollection(ProductResource::collection($query), ProductResource::class);
            });
        }

        $query = Product::query();
        $query->select([
            'products.id',
            'products.name',
            'products.description',
            'products.code',
            'products.price',
        ]);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('products.name', 'like', "%{$search}%");
                $q->orWhere('products.price', 'like', "%{$search}%");
            });
        }

        // sorting
        if (isset($request->sort_by) && isset($request->sort_order) && ! empty($request->sort_by) && ! empty($request->sort_order)) {
            $query->orderBy($request->sort_by, $request->sort_order);
        } else {
            $query->orderBy('products.created_at', 'DESC'); // Default sorting on created at column (latest first)
        }

        // Filter
        $filter = $request->get('filters');
        if (! is_null($filter)) {
            // If filter is already an array (e.g., filters[name]=test), convert to object
            if (is_array($filter)) {
                $filter = (object) $filter;
            } elseif (is_string($filter)) {
                // Check if it's base64 encoded
                $decoded = base64_decode($filter, true);
                if ($decoded !== false && base64_encode($decoded) === $filter) {
                    $filter = json_decode(urldecode($decoded)); // IF YOU USE URL-ENCODING
                } else {
                    $filter = json_decode($filter); // IF YOU DO NOT USE URL-ENCODING
                }
            }

            // Apply filter to query (handle both array and object)
            if (is_array($filter) && ! empty($filter)) {
                foreach ($filter as $key => $value) {
                    if ($value !== null && $value !== '') {
                        $column = (string) $key;
                        $query->where($column, '=', $value);
                    }
                }
            } elseif (is_object($filter) && ! empty((array) $filter)) {
                foreach ($filter as $key => $value) {
                    if ($value !== null && $value !== '') {
                        $column = (string) $key;
                        $query->where($column, '=', $value);
                    }
                }
            }
        }

        // Apply pagination
        $perPage = $request->get('per_page', config('constants.apiPerPage'));
        $page = $request->get('page', config('constants.apiPage'));
        $query = $query->paginate($perPage, ['*'], 'page', $page);

        // Efficiently load hasMany relationships using single optimized queries (better performance than with())
        // Load belongsToMany relationships - Laravel batches queries efficiently
        if (! empty($query->items())) {
            $query->getCollection()->load(['brands']);
        }

        return new ProductCollection(ProductResource::collection($query), ProductResource::class);
    }

    /**
     * Product Detail
     *
     * @return ProductCollection
     */
    public function show(Product $product)
    {
        // Efficiently load hasMany relationships for single record
        // Load hasMany relationships using JOIN query (single query, best performance)
        $query = Product::query();
        $query->where('products.id', $product->id);
        $query->select([
            'products.*',
        ]);
        $query->groupBy('products.id');
        $product = $query->first();
        if ($product) {
            $product->load(['brands']);
        }

        return new ProductCollection(ProductResource::collection([$product]), ProductResource::class);
    }

    /**
     * Add Product
     *
     * @return ProductCollection
     */
    public function store(ProductRequest $request)
    {
        return $this->createModel($request);
    }

    /**
     * Update Product
     *
     * @param int|string $id
     * @return ProductCollection
     */
    public function update(ProductUpdateRequest $request, $id)
    {
        $product = Product::findOrFail($id);

        return $this->updateModel($request, $product);
    }

    /**
     * Delete Product
     *
     * @return DataTrueResource
     */
    public function destroy(Request $request, Product $product)
    {
        return $this->deleteModel($request, $product);
    }

    /**
     * Delete Product multiple
     *
     * @return DataTrueResource|JsonResponse
     */
    public function deleteAll(Request $request)
    {
        return $this->deleteAllModels($request);
    }

    /**
     * Create Product model
     *
     * @return ProductCollection
     */
    protected function createModel(ProductRequest $request)
    {
        /** @var array<string, mixed> $data */
        $data = $request->all();
        $product = Product::create($data);

        // Handle multiple entries for brands (belongsToMany pattern)
        if ($request->has('brands') && is_array($request->brands)) {
            $product->brands()->attach($request->brands);
        }

        // Load hasMany relationships using JOIN query (single query, best performance)
        // Load hasMany relationships using JOIN query (single query, best performance)
        $query = Product::query();
        $query->where('products.id', $product->id);
        $query->select([
            'products.*',
        ]);
        $query->groupBy('products.id');
        $product = $query->first();
        if ($product) {
            $product->load(['brands']);
        }

        return new ProductCollection(ProductResource::collection([$product]), ProductResource::class);
    }

    /**
     * Update Product model
     *
     * @return ProductCollection
     */
    protected function updateModel(ProductUpdateRequest $request, Product $product)
    {
        /** @var array<string, mixed> $data */
        $data = $request->all();

        // Handle multiple entries update for brands (belongsToMany: detach then attach)
        if ($request->has('brands')) {
            $product->brands()->detach();
            if (is_array($request->brands) && ! empty($request->brands)) {
                $product->brands()->attach($request->brands);
            }
        }

        $product->update($data);

        // Load hasMany relationships using JOIN query (single query, best performance)
        // Load hasMany relationships using JOIN query (single query, best performance)
        $query = Product::query();
        $query->where('products.id', $product->id);
        $query->select([
            'products.*',
        ]);
        $query->groupBy('products.id');
        $product = $query->first();
        if ($product) {
            $product->load(['brands']);
        }

        return new ProductCollection(ProductResource::collection([$product]), ProductResource::class);
    }

    /**
     * Delete Product model
     *
     * @return DataTrueResource
     */
    protected function deleteModel(Request $request, Product $product)
    {
        $product->delete();

        return new DataTrueResource($product, trans('messages.api.delete_success', ['model' => 'Product']));
    }

    /**
     * Delete multiple Product models
     *
     * @return DataTrueResource|JsonResponse
     */
    protected function deleteAllModels(Request $request)
    {
        /** @var array<int>|null $ids */
        $ids = $request->ids ?? null;
        if (! empty($ids) && is_array($ids)) {
            Product::whereIn('id', $ids)->get()->each(function ($product) {
                $product->delete();
            });

            return new DataTrueResource(true, trans('messages.api.delete_multiple_success', ['models' => Str::plural('Product')]));
        } else {
            return Product::GetError(trans('messages.api.delete_multiple_error'));
        }
    }
}
