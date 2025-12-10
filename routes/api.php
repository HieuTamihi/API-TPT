<?php
use App\Http\Controllers\ImportsController;
use Illuminate\Support\Facades\Route;

// Group route cho Imports
Route::prefix('imports')->group(function () {
    Route::get('/list', [ImportsController::class, 'list']);           // API Danh sách
    Route::post('/add', [ImportsController::class, 'add']);             // API Tạo mới
    Route::get('/detail/{id}', [ImportsController::class, 'detail']);   // API Xem chi tiết
    Route::put('/change/{id}', [ImportsController::class, 'change']);   // API Cập nhật
    Route::delete('/delete/{id}', [ImportsController::class, 'delete']); // API Xóa
});