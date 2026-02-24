<?php

use Illuminate\Support\Facades\{Auth, Route};
use App\Http\Controllers\{ClientController, CompanyDetailController, FrontendController, PermissionController, ProductController, QuotationController, RoleController, UserController};

Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);


    Route::get('/', [FrontendController::class, 'index'])->name('index')->middleware('auth');


Route::middleware(['auth', 'role:Super Admin'])->group(function () {

    // Administration
    Route::resource('users', UserController::class);
    Route::resource('role', RoleController::class);
    Route::resource('permission', PermissionController::class);
    Route::get('/user/pin', [UserController::class, 'pin'])->name('users.pin');
    Route::post('/user/pin', [UserController::class, 'pinStore'])->name('users.pin_store');

    // Quotations
    Route::resource('quotations', QuotationController::class);
    Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'generatePDF'])->name('quotations.pdf');
    // Route::get('quotations/{quotation}/pdf-preview', [QuotationController::class, 'previewPDF'])->name('quotations.pdf.preview');
    Route::get('/quotations/{quotation}/download', [QuotationController::class, 'download'])->name('quotations.download');
    Route::post('quotations/{quotation}/send', [QuotationController::class, 'sendQuotation'])->name('quotations.send');

    // Products
    Route::resource('products', ProductController::class);

    // Clients
    Route::resource('clients', ClientController::class);

    // Company
    Route::resource('company', CompanyDetailController::class)
        ->parameters(['company' => 'companyDetail']);
    Route::post('company/{companyDetail}/set-default', [CompanyDetailController::class, 'setDefault'])
        ->name('company.set-default');
});
