<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebUserRequest;

use App\Http\Requests\WebUserUpdateRequest;
use App\Http\Resources\DataTrueResource;
use App\Http\Resources\WebUserCollection;
use App\Http\Resources\WebUserResource;
use App\Models\WebUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/*
   |--------------------------------------------------------------------------
   | webusers Controller
   |--------------------------------------------------------------------------
   |
   | This controller handles the webusers of
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

class WebUserAPIController extends Controller
{
    /**
     * list webusers
     *
     * @return WebUserCollection
     */
    public function index(Request $request)
    {
        if ($request->get('is_light', false)) {
            return Cache::rememberForever('webuser.all', function () {
                $webuser = new WebUser();
                /** @var array<string> $lightFields */
                $lightFields = $webuser->light ?? [];
                $query = WebUser::select($lightFields);

                $query = $query->get();

                return new WebUserCollection(WebUserResource::collection($query), WebUserResource::class);
            });
        }

        $query = WebUser::query();
        $query->select([
            'web_users.id',
            'web_users.name',
            'web_users.email',
            'web_users.password',
            'web_users.dob',
            'web_users.country_id',
            'web_users.state_id',
            'web_users.city_id',
            'web_users.gender',
            'web_users.status',
        ]);

        // sorting
        if (isset($request->sort_by) && isset($request->sort_order) && ! empty($request->sort_by) && ! empty($request->sort_order)) {
            $query->orderBy($request->sort_by, $request->sort_order);
        } else {
            $query->orderBy('web_users.created_at', 'DESC'); // Default sorting on created at column (latest first)
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

        return new WebUserCollection(WebUserResource::collection($query), WebUserResource::class);
    }

    /**
     * WebUser Detail
     *
     * @return WebUserCollection
     */
    public function show(WebUser $webuser)
    {
        // Efficiently load hasMany relationships for single record

        return new WebUserCollection(WebUserResource::collection([$webuser]), WebUserResource::class);
    }

    /**
     * Add WebUser
     *
     * @return WebUserCollection
     */
    public function store(WebUserRequest $request)
    {
        return $this->createModel($request);
    }

    /**
     * Update WebUser
     *
     * @param int|string $id
     * @return WebUserCollection
     */
    public function update(WebUserUpdateRequest $request, $id)
    {
        $webuser = WebUser::findOrFail($id);

        return $this->updateModel($request, $webuser);
    }

    /**
     * Delete WebUser
     *
     * @return DataTrueResource
     */
    public function destroy(Request $request, WebUser $webuser)
    {
        return $this->deleteModel($request, $webuser);
    }

    /**
     * Delete WebUser multiple
     *
     * @return DataTrueResource|JsonResponse
     */
    public function deleteAll(Request $request)
    {
        return $this->deleteAllModels($request);
    }

    /**
     * Create WebUser model
     *
     * @return WebUserCollection
     */
    protected function createModel(WebUserRequest $request)
    {
        /** @var array<string, mixed> $data */
        $data = $request->all();
        $webuser = WebUser::create($data);

        // Load hasMany relationships using JOIN query (single query, best performance)

        return new WebUserCollection(WebUserResource::collection([$webuser]), WebUserResource::class);
    }

    /**
     * Update WebUser model
     *
     * @return WebUserCollection
     */
    protected function updateModel(WebUserUpdateRequest $request, WebUser $webuser)
    {
        /** @var array<string, mixed> $data */
        $data = $request->all();

        $webuser->update($data);

        // Load hasMany relationships using JOIN query (single query, best performance)

        return new WebUserCollection(WebUserResource::collection([$webuser]), WebUserResource::class);
    }

    /**
     * Delete WebUser model
     *
     * @return DataTrueResource
     */
    protected function deleteModel(Request $request, WebUser $webuser)
    {
        $webuser->delete();

        return new DataTrueResource($webuser, trans('messages.api.delete_success', ['model' => 'WebUser']));
    }

    /**
     * Delete multiple WebUser models
     *
     * @return DataTrueResource|JsonResponse
     */
    protected function deleteAllModels(Request $request)
    {
        /** @var array<int>|null $ids */
        $ids = $request->ids ?? null;
        if (! empty($ids) && is_array($ids)) {
            WebUser::whereIn('id', $ids)->get()->each(function ($webuser) {
                $webuser->delete();
            });

            return new DataTrueResource(true, trans('messages.api.delete_multiple_success', ['models' => Str::plural('WebUser')]));
        } else {
            return WebUser::GetError(trans('messages.api.delete_multiple_error'));
        }
    }
}
