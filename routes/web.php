<?php

use App\Livewire\Dashboard;
use App\Livewire\Settings\Password;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::post('upload-file', [App\Http\Controllers\API\UserAPIController::class, 'uploadFile'])->name('uploadFile');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');

    // Settings
    Route::get('settings/password', Password::class)->name('settings.password');

    Route::get('email-format', App\Livewire\EmailFormat\Edit::class)->name('email-format');
    Route::get('email-templates', App\Livewire\EmailTemplate\Index::class)->name('email-template.index');
    Route::get('email-template/{id}/edit', App\Livewire\EmailTemplate\Edit::class)->name('email-template.edit');

    // Permission Management
    Route::get('permission', App\Livewire\Permission\Edit::class)->name('permission');

    // SSE Export
    Route::get('export-stream/stream', [App\Http\Controllers\ExportStreamController::class, 'stream'])->name('export-stream.stream');
    Route::get('export-stream/status', [App\Http\Controllers\ExportStreamController::class, 'status'])->name('export-stream.status');
    Route::post('export-stream/cancel', [App\Http\Controllers\ExportStreamController::class, 'cancel'])->name('export-stream.cancel');
    Route::post('export-stream/cleanup', [App\Http\Controllers\ExportStreamController::class, 'cleanup'])->name('export-stream.cleanup');
    Route::get('export-progress/stream', [App\Http\Controllers\ExportProgressController::class, 'stream'])->name('export-progress.stream');
    Route::get('export-progress/download/{batchId}', [App\Http\Controllers\ExportProgressController::class, 'download'])->name('export.download');

    /* Admin - Role Module */
    Route::get('/role', App\Livewire\Role\Index::class)->name('role.index'); // Role Listing
    Route::get('/role-imports', App\Livewire\Role\Import\IndexImport::class)->name('role.imports'); // Import history

    /* Admin - User Module */
    Route::get('/user', App\Livewire\User\Index::class)->name('user.index'); // User Listing
    Route::get('/user/create', App\Livewire\User\Create::class)->name('user.create'); // Create User
    Route::get('/user/{id}/edit', App\Livewire\User\Edit::class)->name('user.edit'); // Edit User
    Route::get('/user-imports', App\Livewire\User\Import\IndexImport::class)->name('user.imports'); // Import history

    /* Admin - Brand Module */
    Route::get('/brand', App\Livewire\Brand\Index::class)->name('brand.index'); // Brand Listing
    Route::get('/brand/create', App\Livewire\Brand\Create::class)->name('brand.create'); // Create Brand
    Route::get('/brand/{id}/edit', App\Livewire\Brand\Edit::class)->name('brand.edit'); // Edit Brand
    Route::get('/brand-imports', App\Livewire\Brand\Import\IndexImport::class)->name('brand.imports'); // Import history

    /* Admin - Product Module */
    Route::get('/product', App\Livewire\Product\Index::class)->name('product.index'); // Product Listing
    Route::get('/product/create', App\Livewire\Product\Create::class)->name('product.create'); // Create Product
    Route::get('/product/{id}/edit', App\Livewire\Product\Edit::class)->name('product.edit'); // Edit Product
    Route::get('/product-imports', App\Livewire\Product\Import\IndexImport::class)->name('product.imports'); // Import history
});

/* Delete Account */
Route::get('delete-account', App\Livewire\DeleteAccount\DeleteAccount::class)->name('delete-account.delete');
Route::get('remove-account', App\Livewire\DeleteAccount\MobileNumber::class)->name('delete-account.remove');
Route::get('verify-otp-file', App\Livewire\DeleteAccount\VerifyOtp::class)->name('delete-account.verify_otp_file');
Route::get('readDatasecurity', App\Livewire\DeleteAccount\ReadDatasecurity::class)->name('delete-account.readDatasecurity');
Route::get('confirmation', App\Livewire\DeleteAccount\Confirmation::class)->name('delete-account.confirmation');
Route::get('success', App\Livewire\DeleteAccount\Success::class)->name('delete-account.success');
