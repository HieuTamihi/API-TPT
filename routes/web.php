<?php

use App\Http\Controllers\CustomersController;
use App\Http\Controllers\ExportsController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\ImportsController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryLookupController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductReturnController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReceivingController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ProvidersController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnFormController;
use App\Http\Controllers\SerialNumberController;
use App\Http\Controllers\WarehouseTransferController;
use App\Http\Controllers\WarrantyLookupController;
use App\Http\Controllers\DatabaseController;
use App\Http\Middleware\CorsMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Database Management Routes (Admin only)
Route::group(['middleware' => ['permission:admin']], function () {
    Route::prefix('database')->group(function () {
        Route::get('/', [DatabaseController::class, 'index'])->name('database.index');
        Route::post('/export', [DatabaseController::class, 'export'])->name('database.export');
        Route::post('/import', [DatabaseController::class, 'import'])->name('database.import');
    });
});

Route::group(['middleware' => ['permission:admin']], function () {
    Route::prefix('admin')->group(function () {
        Route::get('roles-permissions', [RolePermissionController::class, 'index'])->name('roles-permissions.index');

        // Routes cho vai trò
        Route::post('roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');
        Route::put('roles/{id}', [RolePermissionController::class, 'updateRole'])->name('roles.update');
        Route::delete('roles/{id}', [RolePermissionController::class, 'destroyRole'])->name('roles.destroy');

        // Routes cho quyền
        Route::post('permissions', [RolePermissionController::class, 'storePermission'])->name('permissions.store');
        Route::delete('permissions/{id}', [RolePermissionController::class, 'destroyPermission'])->name('permissions.destroy');
    });
});
Route::patch('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
Route::patch('/notifications/mark-all-as-read/{type}', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
Route::get('/notifications/count', function () {
    $count = Auth::user()->unreadNotifications->count();
    return response()->json(['count' => $count]);
});


// Products
Route::resource('products', ProductController::class);
Route::resource('users', UserController::class);
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/edit', [UserController::class, 'profile'])->name('profile.users.edit');
    Route::put('/profile/update', [UserController::class, 'update_profile'])->name('users.update.profile');
});
Route::resource('warehouses', WarehouseController::class);
Route::resource('receivings', ReceivingController::class);
Route::resource('returnforms', ReturnFormController::class);
Route::resource('quotations', QuotationController::class);

// 
Route::get('/get-info-receiving', [ReceivingController::class, 'getReceiving'])->name('getReceiving');
// Check SN
Route::get('/checkSNImport', [SerialNumberController::class, 'checkSNImport'])->name('checkSNImport');
Route::get('/checkSNImportBorrow', [SerialNumberController::class, 'checkSNImportBorrow'])->name('checkSNImportBorrow');
Route::post('/check-serial', [SerialNumberController::class, 'checkSerial']);
Route::get('/check-serial-replace', [SerialNumberController::class, 'checkSNReplace'])->name('checkSNReplace');
Route::post('/check-serial-numbers', [SerialNumberController::class, 'checkSerialNumbers'])->name('check.serial.numbers');
Route::post('/check-serial-batch', [SerialNumberController::class, 'checkSNBatch'])->name('checkSNBatch');

Route::post('/check-serials', [SerialNumberController::class, 'checkSerials'])->name('check.serials');
Route::post('/check-all-warranty', [SerialNumberController::class, 'checkAllWarranty'])->name('check.warranty');
Route::post('/check-brands', [SerialNumberController::class, 'checkbrands'])->name('check.brands');

// Filter
Route::get('/filter-group', [GroupsController::class, 'filterData'])->name('filter-groups');
Route::get('/filter-customer', [CustomersController::class, 'filterData'])->name('filter-customer');
Route::post('/customers/import', [CustomersController::class, 'import'])->name('customers.import');
Route::post('/customers/bulk-confirm', [CustomersController::class, 'bulkConfirm'])->name('customers.bulkConfirm');

Route::post('/providers/import', [ProvidersController::class, 'import'])->name('providers.import');
Route::post('/providers/bulk-confirm', [ProvidersController::class, 'bulkConfirm'])->name('providers.bulkConfirm');

Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
Route::post('/products/bulk-confirm', [ProductController::class, 'bulkConfirm'])->name('products.bulkConfirm');

Route::get('/filter-provides', [ProvidersController::class, 'filterData'])->name('filter-provides');
Route::get('/filter-products', [ProductController::class, 'filterData'])->name('filter-products');
Route::get('/filter-users', [UserController::class, 'filterData'])->name('filter-users');
Route::get('/filter-warehouse1', [WarehouseController::class, 'filterData'])->name('filter-warehouse');
Route::get('/filter-warehouse', [WarehouseTransferController::class, 'filterData'])->name('filter-warehouseTranfer');
Route::get('/filter-imports', [ImportsController::class, 'filterData'])->name('filter-imports');
Route::get('/imports-export', [ImportsController::class, 'export'])->name('imports.export');
Route::get('/filter-exports', [ExportsController::class, 'filterData'])->name('filter-exports');
Route::get('/exports-export', [ExportsController::class, 'export'])->name('exports.export');
Route::get('/filter-receivings', [ReceivingController::class, 'filterData'])->name('filter-receivings');
Route::get('/filter-quotations', [QuotationController::class, 'filterData'])->name('filter-quotations');
Route::get('/filter-returnforms', [ReturnFormController::class, 'filterData'])->name('filter-returnforms');
Route::get('/filter-inven-lookup', [InventoryLookupController::class, 'filterData'])->name('filter-inven-lookup');
Route::get('/filter-warran-lookup', [WarrantyLookupController::class, 'filterData'])->name('filter-warran-lookup');
Route::get('/filter-reports-export-import', [ReportController::class, 'filterExportImport'])->name('reports.export_import');
Route::get('/filter-reports-receipt-return', [ReportController::class, 'filterReceiptReturn'])->name('reports.receipt_return');
Route::get('/filter-reports-quotation', [ReportController::class, 'filterQuotation'])->name('reports.quotation');

Route::post('/update-status', [ReceivingController::class, 'updateStatus'])->name('update.status');

Route::post('/warranty-lookup', [ReceivingController::class, 'warrantyLookup']);


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Groups
Route::resource('groups', GroupsController::class);
Route::get('/dataObj', [GroupsController::class, 'dataObj'])->name('dataObj');
Route::get('/updateDataGroup', [GroupsController::class, 'updateDataGroup'])->name('updateDataGroup');
Route::get('/deleteOJ', [GroupsController::class, 'deleteOJ'])->name('deleteOJ');
//Customers
Route::resource('customers', CustomersController::class);
//Providers
Route::resource('providers', ProvidersController::class);
//Imports
Route::resource('imports', ImportsController::class);
//Exports
Route::resource('exports', ExportsController::class);
//Inventory
Route::middleware('auth')->group(function () {
    Route::resource('inventoryLookup', InventoryLookupController::class);
});
Route::get('/inventory-lookup/summary', [InventoryLookupController::class, 'summary'])->name('inventoryLookup.summary');
//Check S/N exist
Route::get('/checkSN', [SerialNumberController::class, 'checkSN'])->name('checkSN');
//Warranty
Route::resource('warrantyLookup', WarrantyLookupController::class);
//
Route::get('/searchMiniView', [ImportsController::class, 'searchMiniView'])->name('searchMiniView');
//report overview
Route::get('/reportOverview', [ReportController::class, 'reportOverview'])->name('reportOverview');
//report export import
Route::get('/reportExportImport', [ReportController::class, 'reportExportImport'])->name('reportExportImport');
//report receipt return
Route::get('/reportReceiptReturn', [ReportController::class, 'reportReceiptReturn'])->name('reportReceiptReturn');
//report quotation
Route::get('/reportQuotation', [ReportController::class, 'reportQuotation'])->name('reportQuotation');
//
Route::get('/filterReportOverview', [ReportController::class, 'filterReportOverview'])->name('filterReportOverview');
Route::get('/filterReportPeriodTime', [ReportController::class, 'filterReportPeriodTime'])->name('filterReportPeriodTime');

//Report app mobile
Route::middleware([CorsMiddleware::class])->group(function () {
    Route::get('/reportOverviewApp', [ReportController::class, 'reportOverviewApp']);
});
Route::middleware([CorsMiddleware::class])->group(function () {
    Route::get('/reportExportImportApp', [ReportController::class, 'reportExportImportApp']);
});
Route::middleware([CorsMiddleware::class])->group(function () {
    Route::get('/reportReceiptReturnApp', [ReportController::class, 'reportReceiptReturnApp']);
});
Route::middleware([CorsMiddleware::class])->group(function () {
    Route::get('/reportQuotationApp', [ReportController::class, 'reportQuotationApp']);
});

//Warehouse transfer
Route::resource('/warehouseTransfer', WarehouseTransferController::class);

//
Route::post('/save-terms', [QuotationController::class, 'saveTerms'])->name('save-terms');

require __DIR__ . '/auth.php';
