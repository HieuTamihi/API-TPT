<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarrantyLookupSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $data = [
            // 1. Laptop Dell – 3 lần bảo hành/dịch vụ (cùng 1 serial)
            [
                'product_id'              => 1,
                'sn_id'                   => 1,
                'customer_id'             => 1,
                'name_warranty'           => 'Bảo hành chính hãng Dell',
                'export_return_date'      => '2024-07-15 10:30:00',
                'warranty'                => 24,
                'status'                  => 0,
                'name_expire_date'        => 'Bảo hành chính hãng',
                'warranty_expire_date'    => '2026-07-15 23:59:59',
                'warranty_extra'          => null,
                'return_date'             => null,
                'service_warranty_expired'=> null,
                'created_at'              => $now,
                'updated_at'              => $now,
            ],
            [
                'product_id'              => 1,
                'sn_id'                   => 1,
                'customer_id'             => 1,
                'name_warranty'           => 'Gia hạn bảo hành thêm',
                'export_return_date'      => '2026-06-01 14:20:00',
                'warranty'                => 12,
                'status'                  => 0,
                'name_expire_date'        => 'Gia hạn thêm 12 tháng',
                'warranty_expire_date'    => '2027-07-15 23:59:59',
                'warranty_extra'          => 12,
                'return_date'             => null,
                'service_warranty_expired'=> null,
                'created_at'              => $now,
                'updated_at'              => $now,
            ],
            [
                'product_id'              => 1,
                'sn_id'                   => 1,
                'customer_id'             => 1,
                'name_warranty'           => 'Sửa màn hình – Dịch vụ',
                'export_return_date'      => '2025-11-10 09:15:00',
                'warranty'                => null,
                'status'                  => 1,
                'name_expire_date'        => null,
                'warranty_expire_date'    => null,
                'warranty_extra'          => null,
                'return_date'             => '2025-11-15 16:30:00',
                'service_warranty_expired'=> null,
                'created_at'              => $now,
                'updated_at'              => $now,
            ],

            // 2. iPhone 15 Pro Max – AppleCare+
            [
                'product_id'              => 3,
                'sn_id'                   => 2,
                'customer_id'             => 6,
                'name_warranty'           => 'AppleCare+',
                'export_return_date'      => '2024-10-10 09:00:00',
                'warranty'                => 36,
                'status'                  => 0,
                'name_expire_date'        => 'Bảo hành AppleCare+',
                'warranty_expire_date'    => '2027-10-10 23:59:59',
                'warranty_extra'          => null,
                'return_date'             => null,
                'service_warranty_expired'=> null,
                'created_at'              => $now,
                'updated_at'              => $now,
            ],

            // 3. iPhone – Đổi máy mới
            [
                'product_id'              => 3,
                'sn_id'                   => 2,
                'customer_id'             => 6,
                'name_warranty'           => 'Đổi máy mới do lỗi camera',
                'export_return_date'      => '2025-02-20 11:30:00',
                'warranty'                => 36,
                'status'                  => 2,
                'name_expire_date'        => 'Bảo hành máy đổi mới',
                'warranty_expire_date'    => '2028-02-20 23:59:59',
                'warranty_extra'          => null,
                'return_date'             => null,
                'service_warranty_expired'=> null,
                'created_at'              => $now,
                'updated_at'              => $now,
            ],

            // 4. Samsung – Sửa có phí
            [
                'product_id'              => 4,
                'sn_id'                   => 3,
                'customer_id'             => 4,
                'name_warranty'           => 'Sửa mainboard – Có phí',
                'export_return_date'      => '2025-03-15 14:00:00',
                'warranty'                => null,
                'status'                  => 1,
                'name_expire_date'        => null,
                'warranty_expire_date'    => null,
                'warranty_extra'          => null,
                'return_date'             => '2025-03-20 17:00:00',
                'service_warranty_expired'=> null,
                'created_at'              => $now,
                'updated_at'              => $now,
            ],

            // 5. MacBook – Bảo hành chính
            [
                'product_id'              => 2,
                'sn_id'                   => 4,
                'customer_id'             => 2,
                'name_warranty'           => 'Bảo hành Apple 12 tháng',
                'export_return_date'      => '2024-09-20 15:00:00',
                'warranty'                => 12,
                'status'                  => 0,
                'name_expire_date'        => 'Bảo hành chính hãng',
                'warranty_expire_date'    => '2025-09-20 23:59:59',
                'warranty_extra'          => null,
                'return_date'             => null,
                'service_warranty_expired'=> null,
                'created_at'              => $now,
                'updated_at'              => $now,
            ],
        ];

        DB::table('warranty_lookup')->insert($data);
    }
}