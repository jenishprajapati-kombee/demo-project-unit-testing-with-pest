<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::truncate();
        Cache::forget('getAllPermissions');

        Permission::insert([
            ['name' => 'emailformats', 'guard_name' => 'root', 'label' => 'Email Formats', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'view-emailformats', 'guard_name' => 'emailformats', 'label' => 'View', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'edit-emailformats', 'guard_name' => 'emailformats', 'label' => 'Edit', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'emailtemplates', 'guard_name' => 'root', 'label' => 'Email Templates', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'view-emailtemplates', 'guard_name' => 'emailtemplates', 'label' => 'View', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'show-emailtemplates', 'guard_name' => 'emailtemplates', 'label' => 'Show', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'edit-emailtemplates', 'guard_name' => 'emailtemplates', 'label' => 'Edit', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],

            ['name' => 'roles', 'label' => 'Role', 'guard_name' => 'root', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'view-role', 'label' => 'View', 'guard_name' => 'roles', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'show-role', 'label' => 'Show', 'guard_name' => 'roles', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'add-role', 'label' => 'Add', 'guard_name' => 'roles', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'edit-role', 'label' => 'Edit', 'guard_name' => 'roles', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'delete-role', 'label' => 'Delete', 'guard_name' => 'roles', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'bulkDelete-role', 'label' => 'Bulk Delete', 'guard_name' => 'roles', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'import-role', 'label' => 'Import', 'guard_name' => 'roles', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'export-role', 'label' => 'Export', 'guard_name' => 'roles', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],

            ['name' => 'users', 'label' => 'User', 'guard_name' => 'root', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'view-user', 'label' => 'View', 'guard_name' => 'users', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'show-user', 'label' => 'Show', 'guard_name' => 'users', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'add-user', 'label' => 'Add', 'guard_name' => 'users', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'edit-user', 'label' => 'Edit', 'guard_name' => 'users', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'delete-user', 'label' => 'Delete', 'guard_name' => 'users', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'bulkDelete-user', 'label' => 'Bulk Delete', 'guard_name' => 'users', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'import-user', 'label' => 'Import', 'guard_name' => 'users', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'export-user', 'label' => 'Export', 'guard_name' => 'users', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],

            ['name' => 'brands', 'label' => 'Brand', 'guard_name' => 'root', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'view-brand', 'label' => 'View', 'guard_name' => 'brands', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'show-brand', 'label' => 'Show', 'guard_name' => 'brands', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'add-brand', 'label' => 'Add', 'guard_name' => 'brands', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'edit-brand', 'label' => 'Edit', 'guard_name' => 'brands', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'delete-brand', 'label' => 'Delete', 'guard_name' => 'brands', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'bulkDelete-brand', 'label' => 'Bulk Delete', 'guard_name' => 'brands', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'import-brand', 'label' => 'Import', 'guard_name' => 'brands', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'export-brand', 'label' => 'Export', 'guard_name' => 'brands', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],

            ['name' => 'products', 'label' => 'Product', 'guard_name' => 'root', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'view-product', 'label' => 'View', 'guard_name' => 'products', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'show-product', 'label' => 'Show', 'guard_name' => 'products', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'add-product', 'label' => 'Add', 'guard_name' => 'products', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'edit-product', 'label' => 'Edit', 'guard_name' => 'products', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'delete-product', 'label' => 'Delete', 'guard_name' => 'products', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'bulkDelete-product', 'label' => 'Bulk Delete', 'guard_name' => 'products', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'import-product', 'label' => 'Import', 'guard_name' => 'products', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
            ['name' => 'export-product', 'label' => 'Export', 'guard_name' => 'products', 'created_at' => config('constants.calender.date_time'), 'updated_at' => config('constants.calender.date_time')],
        ]);
    }
}
