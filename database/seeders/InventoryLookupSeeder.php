<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryLookupSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            // 1. Laptop Dell - Còn tồn kho tại kho Hà Nội
            [
                'product_id'        => 1,
                'sn_id'             => 1001,
                'provider_id'       => 1,
                'import_date'       => '2024-07-10 14:30:00',
                'storage_duration'  => $now->diffInDays('2024-07-10'),
                'status'            => 1, // 1 = đang tồn
                'warranty_date'     => '2026-07-10 23:59:59',
                'note'              => 'Nhập lô tháng 7 từ FPT',
                'remaining_quantity'=> 5,
                'import_id'         => 501,
                'warehouse_id'      => 1,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],

            // 2. iPhone 15 Pro Max - Đã bán
            [
                'product_id'        => 3,
                'sn_id'             => 2001,
                'provider_id'       => 2,
                'import_date'       => '2024-10-05 09:15:00',
                'storage_duration'  => 0,
                'status'            => 2, // đã bán
                'warranty_date'     => '2025-10-05 23:59:59',
                'note'              => 'Bán cho chị Lan - HĐ: HD241005',
                'remaining_quantity'=> 0,
                'import_id'         => 502,
                'warehouse_id'      => 1,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],

            // 3. Samsung S24 Ultra - Tồn lâu (>150 ngày)
            [
                'product_id'        => 4,
                'sn_id'             => 3001,
                'provider_id'       => 1,
                'import_date'       => '2024-06-01 11:00:00',
                'storage_duration'  => $now->diffInDays('2024-06-01'),
                'status'            => 1,
                'warranty_date'     => '2026-06-01 23:59:59',
                'note'              => 'TỒN LÂU - Cần giảm giá bán',
                'remaining_quantity'=> 1,
                'import_id'         => 503,
                'warehouse_id'      => 2, // kho TP.HCM
                'created_at'        => now(),
                'updated_at'        => now(),
            ],

            // 4. MacBook Air M2 - Đang bảo hành
            [
                'product_id'        => 2,
                'sn_id'             => 4001,
                'provider_id'       => 3,
                'import_date'       => '2024-09-20 16:45:00',
                'storage_duration'  => 0,
                'status'            => 3, // đang bảo hành
                'warranty_date'     => '2026-09-20 23:59:59',
                'note'              => 'Khách gửi sửa mainboard',
                'remaining_quantity'=> 0,
                'import_id'         => 504,
                'warehouse_id'      => 1,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],

            // 5. TV Sony 55" - Hết bảo hành, tồn lỗi
            [
                'product_id'        => 5,
                'sn_id'             => 5001,
                'provider_id'       => 2,
                'import_date'       => '2023-11-15 08:00:00',
                'storage_duration'  => $now->diffInDays('2023-11-15'),
                'status'            => 4, // lỗi/hỏng
                'warranty_date'     => '2024-11-15 00:00:00',
                'note'              => 'Lỗi panel, đang chờ trả hãng',
                'remaining_quantity'=> 0,
                'import_id'         => 505,
                'warehouse_id'      => 1,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ];

        DB::table('inventory_lookup')->insert($data);
    }
}