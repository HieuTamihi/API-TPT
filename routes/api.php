<?php
use App\Http\Controllers\ImportsController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\ProvidersController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

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
    // Route::get('/{id}', [ProductController::class, 'detail']);
    // Route::post('/', [ProductController::class, 'add']);
    // Route::put('/{id}', [ProductController::class, 'update']);
});

// === QUẢN LÝ NGƯỜI DÙNG / NHÂN VIÊN ===
Route::prefix('users')->group(function () {
    Route::get('/roles', [UserController::class, 'roles']);            // Danh sách vai trò (Spatie)
    Route::get('/groups', [UserController::class, 'groups']);          // Nhóm nhân viên
    Route::get('/', [UserController::class, 'list']);                 // Danh sách + lọc
    Route::delete('/{id}', [UserController::class, 'delete']);        // Xóa nhân viên
    // Nếu cần thêm tạo/sửa sau:
    // Route::post('/', [UserController::class, 'store']);
    // Route::put('/{id}', [UserController::class, 'update']);
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