<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DataJsonResponse extends JsonResource
{
    /**
     * The custom resource instance.
     *
     * @var mixed
     */
    public $customResource;

    /**
     * Create a new resource instance.
     *
     * @param mixed $resource
     * @param mixed $customResource
     */
    public function __construct($resource, $customResource)
    {
        parent::__construct($resource);
        $this->customResource = $customResource;
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $resourceClass = $this->customResource;

        if ($request->get('is_light', false)) {
            return [
                'message' => '',
                'data' => $this->collection->transform(function ($item) use ($resourceClass) {
                    return new $resourceClass($item);
                }),
            ];
        }

        // Check if resource is a Paginator instance
        if ($this->resource instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
            /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator */
            $paginator = $this->resource;

            return [
                'current_page' => $paginator->currentPage(),
                'message' => '',
                'total' => $paginator->total(),
                'data' => $this->collection->transform(
                    fn ($item) => new $resourceClass($item)
                ),
                'first_page_url' => url($request->path()) . '?page=1',
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'last_page_url' => url($request->path()) . '?page=' . $paginator->lastPage(),
                'next_page_url' => $paginator->nextPageUrl(),
                'path' => url($request->path()),
                'per_page' => $paginator->perPage(),
                'prev_page_url' => $paginator->previousPageUrl(),
                'to' => $paginator->lastItem(),
            ];
        }

        // Handle Collection (for show, create, update methods)
        return [
            'message' => '',
            'data' => $this->collection->transform(
                fn ($item) => new $resourceClass($item)
            ),
        ];
    }
}
