<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class GlobalHelper
{
    // Helper function to get warehouse_id based on role
    public static function getWarehouseId()
    {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (Auth::check()) {
            $roleUser = Auth::user()->roles()->first()->id;
            return $roleUser == 2 ? 1 : ($roleUser == 3 ? 2 : null);
        }
        // Trả về null hoặc giá trị mặc định nếu người dùng chưa đăng nhập
        return null;
    }
}
