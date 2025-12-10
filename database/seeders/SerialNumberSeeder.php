<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SerialNumberSeeder extends Seeder
{
    public function run(): void
    {
        $serials = [
            // LAPTOP DELL
            ['product_id' => 1, 'serial_code' => 'DELL3511-001A', 'status' => 1, 'warehouse_id' => 1, 'note' => 'Nhập kho Hà Nội 15/07/2024'],
            ['product_id' => 1, 'serial_code' => 'DELL3511-002B', 'status' => 2, 'warehouse_id' => 1, 'note' => 'Đã xuất bán - HD2408012'],
            ['product_id' => 1, 'serial_code' => 'DELL3511-003C', 'status' => 3, 'warehouse_id' => 1, 'note' => 'Khách gửi bảo hành lỗi màn hình'],
            ['product_id' => 1, 'serial_code' => 'DELL3511-004D', 'status' => 5, 'warehouse_id' => 2, 'note' => 'Đang cho mượn trưng bày cửa hàng TP.HCM'],

            // MACBOOK AIR M2
            ['product_id' => 2, 'serial_code' => 'MACM2-2024-01', 'status' => 1, 'warehouse_id' => 2, 'note' => 'Tồn kho TP.HCM'],
            ['product_id' => 2, 'serial_code' => 'MACM2-2024-02', 'status' => 2, 'warehouse_id' => 2, 'note' => 'Bán ngày 01/11/2024'],

            // IPHONE 15 PRO MAX
            ['product_id' => 3, 'serial_code' => 'IPH15PM256-001', 'status' => 1, 'warehouse_id' => 1, 'note' => 'Màu Titan Black'],
            ['product_id' => 3, 'serial_code' => 'IPH15PM256-002', 'status' => 1, 'warehouse_id' => 1, 'note' => 'Màu Titan Blue'],
            ['product_id' => 3, 'serial_code' => 'IPH15PM256-003', 'status' => 4, 'warehouse_id' => 1, 'note' => 'Khách trả hàng do đổi màu'],
            ['product_id' => 3, 'serial_code' => 'IPH15PM1TB-001', 'status' => 2, 'warehouse_id' => 1, 'note' => 'Bán VIP - HĐ2411005'],

            // SAMSUNG S24 ULTRA
            ['product_id' => 4, 'serial_code' => 'S24U512GB-GRAY01', 'status' => 1, 'warehouse_id' => 2, 'note' => 'Tồn lâu >120 ngày'],
            ['product_id' => 4, 'serial_code' => 'S24U512GB-BLACK02', 'status' => 3, 'warehouse_id' => 2, 'note' => 'Đang sửa camera tại TTBH Samsung'],

            // TV SONY 55 INCH
            ['product_id' => 5, 'serial_code' => 'SONY55X90L-001', 'status' => 1, 'warehouse_id' => 1, 'note' => 'Nhập kho trưng bày'],
            ['product_id' => 5, 'serial_code' => 'SONY55X90L-002', 'status' => 2, 'warehouse_id' => 1, 'note' => 'Bán kèm giá treo tường'],

            // THÊM VÀI SERIAL ĐỂ TEST TỒN LÂU
            ['product_id' => 1, 'serial_code' => 'DELL-OLD-2023-01', 'status' => 1, 'warehouse_id' => 1, 'note' => 'Tồn từ 2023 - cần thanh lý'],
            ['product_id' => 3, 'serial_code' => 'IPH14PM-OLD001', 'status' => 1, 'warehouse_id' => 2, 'note' => 'iPhone 14 Pro Max cũ, tồn kho'],
        ];

        foreach ($serials as $serial) {
            DB::table('serial_numbers')->updateOrInsert(
                ['serial_code' => $serial['serial_code']],
                $serial + ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}