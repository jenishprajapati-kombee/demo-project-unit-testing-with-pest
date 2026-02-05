<?php

namespace App\Services\Export;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class UserExportService implements ExportServiceInterface
{
    /**
     * Build export query (mirrors table datasource logic)
     */
    public function buildQuery(array $filters, array $checkboxValues, ?string $search): Builder
    {
        $query = User::query()

            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('countries', 'countries.id', '=', 'users.country_id')
            ->leftJoin('states', 'states.id', '=', 'users.state_id')
            ->leftJoin('cities', 'cities.id', '=', 'users.city_id')
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'roles.name as role_name',
                'users.dob',
                'users.profile',
                'countries.name as country_name',
                'states.name as state_name',
                'cities.name as city_name',
                DB::raw(
                    '(CASE
                        WHEN users.gender = "' . config('constants.user.gender.key.female') . '" THEN  "' . config('constants.user.gender.value.female') . '"
                        WHEN users.gender = "' . config('constants.user.gender.key.male') . '" THEN  "' . config('constants.user.gender.value.male') . '"
                        ELSE " "
                    END) AS gender'
                ),
                DB::raw(
                    '(CASE
                        WHEN users.status = "' . config('constants.user.status.key.active') . '" THEN  "' . config('constants.user.status.value.active') . '"
                        WHEN users.status = "' . config('constants.user.status.key.inactive') . '" THEN  "' . config('constants.user.status.value.inactive') . '"
                        ELSE " "
                    END) AS status'
                ),
            ])
            ->whereNull('users.deleted_at')
            ->groupBy('users.id');

        // Apply name filters
        if (isset($filters['input_text']['users']['name']) && $filters['input_text']['users']['name']) {
            $query->where('users.name', 'like', '%' . $filters['input_text']['users']['name'] . '%');
        }

        // Apply email filters
        if (isset($filters['input_text']['users']['email']) && $filters['input_text']['users']['email']) {
            $query->where('users.email', 'like', '%' . $filters['input_text']['users']['email'] . '%');
        }

        // Apply dob filters
        $where_start = $filters['datetime']['users']['dob']['start'] ?? null;
        $where_end = $filters['datetime']['users']['dob']['end'] ?? null;
        if ($where_start && $where_end) {
            $query->whereBetween('users.dob', [$where_start, $where_end]);
        }

        // Apply country_id filters
        if (isset($filters['select']['users']['country_id']) && $filters['select']['users']['country_id']) {
            $query->where('users.country_id', $filters['select']['users']['country_id']);
        }

        // Apply state_id filters
        if (isset($filters['select']['users']['state_id']) && $filters['select']['users']['state_id']) {
            $query->where('users.state_id', $filters['select']['users']['state_id']);
        }

        // Apply city_id filters
        if (isset($filters['select']['users']['city_id']) && $filters['select']['users']['city_id']) {
            $query->where('users.city_id', $filters['select']['users']['city_id']);
        }

        // Apply gender filters
        if (isset($filters['select']['users']['gender']) && $filters['select']['users']['gender']) {
            $query->where('users.gender', $filters['select']['users']['gender']);
        }

        // Apply status filters
        if (isset($filters['select']['users']['status']) && $filters['select']['users']['status']) {
            $query->where('users.status', $filters['select']['users']['status']);
        }

        // Apply checkbox filter (export only selected ids)
        if (! empty($checkboxValues)) {
            $query->whereIn('users.id', $checkboxValues);
        }

        // Apply global search across configured columns
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('users.name', 'like', "%{$search}%");
                $q->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('users.id', 'desc');
    }

    /**
     * Map a single row to CSV format.
     */
    public function formatToCSV($row): string
    {
        $fields = [
            $row->id ?? '',
            $row->name ?? '',
            $row->email ?? '',
            $row->role_name ?? '',
            $row->dob ?? '',
            $row->profile ?? '',
            $row->country_name ?? '',
            $row->state_name ?? '',
            $row->city_name ?? '',
            $row->gender ?? '',
            $row->status ?? '',
        ];

        return implode(',', array_map([$this, 'wrapInQuotes'], $fields));
    }

    public function getCSVHeader(): string
    {
        return '"Id","Name","Email","Role Id","Dob","Profile","Country Id","State Id","City Id","Gender","Status"';
    }

    public function getFilenamePrefix(): string
    {
        return 'UserReports_';
    }

    public function hasPermission(): bool
    {
        return Gate::allows('view-user');
    }

    /**
     * Wrap value in quotes for CSV compatibility
     */
    private function wrapInQuotes($value): string
    {
        $value = (string) ($value ?? '');

        return '"' . str_replace('"', '""', $value) . '"';
    }
}
