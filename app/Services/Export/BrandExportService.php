<?php

namespace App\Services\Export;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class BrandExportService implements ExportServiceInterface
{
    /**
     * Build export query (mirrors table datasource logic)
     */
    public function buildQuery(array $filters, array $checkboxValues, ?string $search): Builder
    {
        $query = Brand::query()

            ->leftJoin('countries', 'countries.id', '=', 'brands.country_id')
            ->leftJoin('states', 'states.id', '=', 'brands.state_id')
            ->leftJoin('cities', 'cities.id', '=', 'brands.city_id')
            ->select([
                'brands.id',
                'brands.name',
                'brands.remark',
                'brands.bob',
                'brands.description',
                'countries.name as country_name',
                'states.name as state_name',
                'cities.name as city_name',
                DB::raw(
                    '(CASE
                        WHEN brands.status = "' . config('constants.brand.status.key.active') . '" THEN  "' . config('constants.brand.status.value.active') . '"
                        WHEN brands.status = "' . config('constants.brand.status.key.inactive') . '" THEN  "' . config('constants.brand.status.value.inactive') . '"
                        ELSE " "
                    END) AS status'
                ),
            ])
            ->whereNull('brands.deleted_at')
            ->groupBy('brands.id');

        // Apply name filters
        if (isset($filters['input_text']['brands']['name']) && $filters['input_text']['brands']['name']) {
            $query->where('brands.name', 'like', '%' . $filters['input_text']['brands']['name'] . '%');
        }

        // Apply remark filters
        if (isset($filters['input_text']['brands']['remark']) && $filters['input_text']['brands']['remark']) {
            $query->where('brands.remark', 'like', '%' . $filters['input_text']['brands']['remark'] . '%');
        }

        // Apply bob filters
        $where_bob = $filters['datetime']['brands']['bob'] ?? null;
        if ($where_bob) {
            $query->whereBetween('brands.bob', [$where_bob['start'], $where_bob['end']]);
        }

        // Apply country_id filters
        if (isset($filters['select']['brands']['country_id']) && $filters['select']['brands']['country_id']) {
            $query->where('brands.country_id', $filters['select']['brands']['country_id']);
        }

        // Apply state_id filters
        if (isset($filters['select']['brands']['state_id']) && $filters['select']['brands']['state_id']) {
            $query->where('brands.state_id', $filters['select']['brands']['state_id']);
        }

        // Apply city_id filters
        if (isset($filters['select']['brands']['city_id']) && $filters['select']['brands']['city_id']) {
            $query->where('brands.city_id', $filters['select']['brands']['city_id']);
        }

        // Apply status filters
        if (isset($filters['select']['brands']['status']) && $filters['select']['brands']['status']) {
            $query->where('brands.status', $filters['select']['brands']['status']);
        }

        // Apply checkbox filter (export only selected ids)
        if (! empty($checkboxValues)) {
            $query->whereIn('brands.id', $checkboxValues);
        }

        // Apply global search across configured columns
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('brands.name', 'like', "%{$search}%");
                $q->orWhere('brands.remark', 'like', "%{$search}%");
                $q->orWhere('brands.description', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('brands.id', 'desc');
    }

    /**
     * Map a single row to CSV format.
     */
    public function formatToCSV($row): string
    {
        $fields = [
            $row->id ?? '',
            $row->name ?? '',
            $row->remark ?? '',
            $row->bob ?? '',
            $row->description ?? '',
            $row->country_name ?? '',
            $row->state_name ?? '',
            $row->city_name ?? '',
            $row->status ?? '',
        ];

        return implode(',', array_map([$this, 'wrapInQuotes'], $fields));
    }

    public function getCSVHeader(): string
    {
        return '"Id","Name","Remark","Bob","Description","Country Id","State Id","City Id","Status"';
    }

    public function getFilenamePrefix(): string
    {
        return 'BrandReports_';
    }

    public function hasPermission(): bool
    {
        return Gate::allows('view-brand');
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
