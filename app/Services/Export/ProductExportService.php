<?php

namespace App\Services\Export;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class ProductExportService implements ExportServiceInterface
{
    /**
     * Build export query (mirrors table datasource logic)
     */
    public function buildQuery(array $filters, array $checkboxValues, ?string $search): Builder
    {
        $query = Product::query()

            ->select([
                'products.id',
                'products.name',
                'products.price',
            ])
            ->whereNull('products.deleted_at')
            ->groupBy('products.id');

        // Apply name filters
        if (isset($filters['input_text']['products']['name']) && $filters['input_text']['products']['name']) {
            $query->where('products.name', 'like', '%' . $filters['input_text']['products']['name'] . '%');
        }

        // Apply price filters
        if (isset($filters['input_text']['products']['price']) && $filters['input_text']['products']['price']) {
            $query->where('products.price', 'like', '%' . $filters['input_text']['products']['price'] . '%');
        }

        // Apply checkbox filter (export only selected ids)
        if (! empty($checkboxValues)) {
            $query->whereIn('products.id', $checkboxValues);
        }

        // Apply global search across configured columns
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('products.name', 'like', "%{$search}%");
                $q->orWhere('products.price', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('products.id', 'desc');
    }

    /**
     * Map a single row to CSV format.
     */
    public function formatToCSV($row): string
    {
        $fields = [
            $row->id ?? '',
            $row->name ?? '',
            $row->price ?? '',
        ];

        return implode(',', array_map([$this, 'wrapInQuotes'], $fields));
    }

    public function getCSVHeader(): string
    {
        return '"Id","Name","Price"';
    }

    public function getFilenamePrefix(): string
    {
        return 'ProductReports_';
    }

    public function hasPermission(): bool
    {
        return Gate::allows('view-product');
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
