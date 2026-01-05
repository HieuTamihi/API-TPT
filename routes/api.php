<?php

use App\Http\Controllers\ImportsController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\ProvidersController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ExportsController;
use App\Http\Controllers\WarehouseTransferController;
use App\Http\Controllers\ReturnFormController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReceivingController;
use App\Http\Controllers\InventoryLookupController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WarrantyLookupController;
use Illuminate\Support\Facades\Route;

Route::prefix('reports')->group(function () {
    // 1. Tổng quát
    Route::get('/overview', [ReportController::class, 'reportOverviewApp']);
    Route::get('/filter-period', [ReportController::class, 'filterReportPeriodTime']); // API lọc thời gian tổng quát

    // 2. Xuất nhập
    Route::get('/export-import', [ReportController::class, 'reportExportImportApp']);
    Route::get('/filter-export-import', [ReportController::class, 'filterExportImport']);

    // 3. Tiếp nhận - Trả hàng
    Route::get('/receipt-return', [ReportController::class, 'reportReceiptReturnApp']);
    Route::get('/filter-receipt-return', [ReportController::class, 'filterReceiptReturn']);

    // 4. Báo giá
    Route::get('/quotation', [ReportController::class, 'reportQuotationApp']);
    Route::get('/filter-quotation', [ReportController::class, 'filterQuotation']);
});

// === Quản Lý TRẢ HÀNG (Return Forms) ===
Route::prefix('return-forms')->group(function () {
    // 1. Lấy danh sách (kèm lọc, tìm kiếm)
    Route::get('/list', [ReturnFormController::class, 'list']);

    // 2. Chi tiết
    Route::get('/detail/{id}', [ReturnFormController::class, 'detail']);

    // 3. Tạo mới
    Route::post('/add', [ReturnFormController::class, 'add']);

    // 4. Cập nhật thông tin
    Route::put('/change/{id}', [ReturnFormController::class, 'change']);

    // 5. Xóa phiếu trả hàng
    Route::delete('/delete/{id}', [ReturnFormController::class, 'delete']);
});

// === Quản Lý BÁO GIÁ (Quotations) ===
Route::prefix('quotations')->group(function () {
    // 1. Lấy danh sách (kèm lọc, tìm kiếm)
    Route::get('/list', [QuotationController::class, 'list']);

    // 2. Chi tiết
    Route::get('/detail/{id}', [QuotationController::class, 'detail']);

    // 3. Tạo mới
    Route::post('/add', [QuotationController::class, 'add']);

    // 4. Cập nhật thông tin
    Route::put('/change/{id}', [QuotationController::class, 'change']);

    // 5. Xóa báo giá
    Route::delete('/delete/{id}', [QuotationController::class, 'delete']);
});

// === QUẢN LÝ TIẾP NHẬN (Receivings) ===
Route::prefix('receivings')->group(function () {
    // 1. Lấy danh sách (kèm lọc, tìm kiếm)
    Route::get('/list', [ReceivingController::class, 'list']);

    // 2. Chi tiết
    Route::get('/detail/{id}', [ReceivingController::class, 'detail']);

    // 3. Tạo mới
    Route::post('/add', [ReceivingController::class, 'add']);

    // 4. Cập nhật thông tin
    Route::put('/change/{id}', [ReceivingController::class, 'change']);

    // 5. Xóa phiếu
    Route::delete('/delete/{id}', [ReceivingController::class, 'delete']);

    // 6. Cập nhật trạng thái nhanh (Dùng cho Modal tác vụ)
    Route::post('/update-status', [ReceivingController::class, 'updateStatus']);
});

// === QUẢN LÝ BẢO HÀNH (Warranty Lookup) ===
Route::prefix('warranty')->group(function () {
    Route::get('/', [WarrantyLookupController::class, 'list']);                    // Danh sách + lọc
    Route::get('/customers', [WarrantyLookupController::class, 'customers']);      // Dropdown khách hàng
    Route::get('/{serial_id}', [WarrantyLookupController::class, 'detail']);       // Chi tiết theo S/N
    Route::post('/', [WarrantyLookupController::class, 'add']);                  // Tạo mới
    Route::put('/{id}', [WarrantyLookupController::class, 'change']);              // Cập nhật
    Route::delete('/{id}', [WarrantyLookupController::class, 'delete']);           // Xóa
});


// === TRA CỨU TỒN KHO (Inventory Lookup) ===
Route::prefix('inventory')->group(function () {
    Route::get('/', [InventoryLookupController::class, 'list']);           // Danh sách tồn kho + lọc
    Route::get('/check', [InventoryLookupController::class, 'checkStock']); // Dropdown sản phẩm
    Route::get('/providers', [InventoryLookupController::class, 'providers']); // Dropdown NCC
    Route::get('/warehouses', [InventoryLookupController::class, 'warehouses']); // Dropdown kho
});

// === 1. CHUYỂN KHO (Warehouse Transfer) ===
Route::prefix('warehouse-transfer')->group(function () {
    Route::get('/list', [WarehouseTransferController::class, 'list']);          // Danh sách
    Route::get('/detail/{id}', [WarehouseTransferController::class, 'detail']); // Chi tiết
    Route::post('/add', [WarehouseTransferController::class, 'add']);           // Tạo mới
    Route::put('/change/{id}', [WarehouseTransferController::class, 'change']); // Cập nhật
    Route::delete('/delete/{id}', [WarehouseTransferController::class, 'delete']); // Xóa
});

// === 2. PHIẾU TRẢ HÀNG (Return Form) ===
Route::prefix('return-form')->group(function () {
    Route::get('/list', [ReturnFormController::class, 'list']);
    Route::get('/detail/{id}', [ReturnFormController::class, 'detail']);
    Route::post('/add', [ReturnFormController::class, 'add']);
    Route::put('/change/{id}', [ReturnFormController::class, 'change']);
    Route::delete('/delete/{id}', [ReturnFormController::class, 'delete']);
});

// === 3. BÁO GIÁ (Quotations) ===
Route::prefix('quotations')->group(function () {
    Route::get('/list', [QuotationController::class, 'list']);
    Route::get('/detail/{id}', [QuotationController::class, 'detail']);
    Route::post('/add', [QuotationController::class, 'add']);
    Route::put('/change/{id}', [QuotationController::class, 'change']);
    Route::delete('/delete/{id}', [QuotationController::class, 'delete']);
});

// === 4. NHẬP KHO / TIẾP NHẬN (Receivings) ===
Route::prefix('receivings')->group(function () {
    Route::get('/list', [ReceivingController::class, 'list']);
    Route::get('/detail/{id}', [ReceivingController::class, 'detail']);
    Route::post('/add', [ReceivingController::class, 'add']);
    Route::put('/change/{id}', [ReceivingController::class, 'change']);
    Route::delete('/delete/{id}', [ReceivingController::class, 'delete']);
});

// === 5. NHẬP HÀNG (Imports) ===
Route::prefix('imports')->group(function () {
    Route::get('/list', [ImportsController::class, 'list']);
    Route::get('/detail/{id}', [ImportsController::class, 'detail']);
    Route::post('/add', [ImportsController::class, 'add']);
    Route::put('/change/{id}', [ImportsController::class, 'change']);
    Route::delete('/delete/{id}', [ImportsController::class, 'delete']);
    Route::post('/import-excel', [ImportsController::class, 'importExcel']);
});

// === 6. XUẤT HÀNG (Exports) ===
Route::prefix('exports')->group(function () {
    Route::get('/list', [ExportsController::class, 'list']);
    Route::get('/detail/{id}', [ExportsController::class, 'detail']);
    Route::post('/add', [ExportsController::class, 'add']);
    Route::put('/change/{id}', [ExportsController::class, 'change']);
    Route::delete('/delete/{id}', [ExportsController::class, 'delete']);
});

// === QUẢN LÝ NHÓM ĐỐI TƯỢNG (Groups) ===
Route::prefix('groups')->group(function () {
    Route::get('/types', [GroupsController::class, 'types']);           // Danh sách loại nhóm
    Route::get('/', [GroupsController::class, 'list']);                 // Danh sách nhóm + tìm kiếm
    Route::get('/{id}', [GroupsController::class, 'detail']);           // Chi tiết
    Route::post('/', [GroupsController::class, 'add']);                 // Tạo mới
    Route::put('/{id}', [GroupsController::class, 'change']);           // Cập nhật
    Route::delete('/{id}', [GroupsController::class, 'delete']);        // Xóa
});

// === QUẢN LÝ KHÁCH HÀNG ===
Route::prefix('customers')->group(function () {
    Route::get('/groups', [CustomersController::class, 'groups']);      // Nhóm khách hàng
    Route::get('/', [CustomersController::class, 'list']);              // Danh sách + lọc
    Route::get('/{id}', [CustomersController::class, 'detail']);        // Chi tiết
    Route::post('/', [CustomersController::class, 'add']);             // Tạo mới
    Route::put('/{id}', [CustomersController::class, 'change']);        // Cập nhật
    Route::delete('/{id}', [CustomersController::class, 'detele']);     // Xóa (sửa tên từ detele → delete nếu muốn)
});

// === QUẢN LÝ NHÀ CUNG CẤP ===
Route::prefix('providers')->group(function () {
    Route::get('/groups', [ProvidersController::class, 'groups']);     // Nhóm nhà cung cấp
    Route::get('/', [ProvidersController::class, 'list']);             // Danh sách + lọc
    Route::get('/{id}', [ProvidersController::class, 'detail']);       // Chi tiết
    Route::post('/', [ProvidersController::class, 'add']);             // Tạo mới
    Route::put('/{id}', [ProvidersController::class, 'change']);       // Cập nhật
    Route::delete('/{id}', [ProvidersController::class, 'delete']);    // Xóa
});

// === QUẢN LÝ SẢN PHẨM / HÀNG HÓA ===
Route::prefix('products')->group(function () {
    Route::get('/groups', [ProductController::class, 'groups']);       // Nhóm hàng hóa
    Route::get('/', [ProductController::class, 'list']);              // Danh sách + lọc + nhóm
    Route::delete('/{id}', [ProductController::class, 'delete']);     // Xóa sản phẩm
    // Nếu cần thêm tạo/sửa/chi tiết sau này:
    Route::get('/{id}', [ProductController::class, 'detail']);
    Route::post('/', [ProductController::class, 'add']);
    Route::put('/{id}', [ProductController::class, 'change']);
});

// === QUẢN LÝ NGƯỜI DÙNG / NHÂN VIÊN ===
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'list']);                 // Lấy danh sách (GET /api/users)
    Route::post('/', [UserController::class, 'add']);               // Tạo mới (POST /api/users)
    Route::put('/{id}', [UserController::class, 'change']);           // Cập nhật (PUT /api/users/{id})
    Route::delete('/{id}', [UserController::class, 'delete']);        // Xóa (DELETE /api/users/{id})

    // Các route phụ trợ
    Route::get('/roles', [UserController::class, 'roles']);           // Lấy danh sách Role
    Route::get('/groups', [UserController::class, 'groups']);         // Lấy danh sách Nhóm NV
});

// === QUẢN LÝ KHO ===
Route::prefix('warehouses')->group(function () {
    Route::get('/', [WarehouseController::class, 'list']);            // Danh sách + lọc
    Route::get('/{id}', [WarehouseController::class, 'detail']);      // Chi tiết
    Route::post('/', [WarehouseController::class, 'add']);            // Tạo mới
    Route::put('/{id}', [WarehouseController::class, 'change']);      // Cập nhật
    Route::delete('/{id}', [WarehouseController::class, 'delete']);   // Xóa
});

// === CẬP NHẬT HỒ SƠ CÁ NHÂN (nếu cần API riêng) ===
Route::post('/profile/update', [UserController::class, 'update_profile']);

// Group route cho Imports
Route::prefix('imports')->group(function () {
    Route::get('/list', [ImportsController::class, 'list']);           // API Danh sách
    Route::post('/add', [ImportsController::class, 'add']);             // API Tạo mới
    Route::get('/detail/{id}', [ImportsController::class, 'detail']);   // API Xem chi tiết
    Route::put('/change/{id}', [ImportsController::class, 'change']);   // API Cập nhật
    Route::delete('/delete/{id}', [ImportsController::class, 'delete']); // API Xóa
});
