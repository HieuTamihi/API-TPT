<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductReturnSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_returns')->insertOrIgnore([
            [
                'return_form_id'            => 90001,
                'product_id'                => 1,
                'quantity'                  => 1,
                'serial_number_id'          => 1001,
                'replacement_code'          => null,
                'replacement_serial_number_id' => null,
                'extra_warranty'            => null,
                'notes'                     => 'Lỗi màn hình nhấp nháy, đã hoàn tiền',
            ],
            [
                'return_form_id'            => 90002,
                'product_id'                => 3,
                'quantity'                  => 1,
                'serial_number_id'          => 2001,
                'replacement_code'          => 'PHN002',
                'replacement_serial_number_id' => 3001,
                'extra_warranty'            => 6,
                'notes'                     => 'Khách đổi sang Samsung, tặng thêm 6 tháng bảo hành',
            ],
            [
                'return_form_id'            => 90003,
                'product_id'                => 2,
                'quantity'                  => 1,
                'serial_number_id'          => 1002,
                'replacement_code'          => null,
                'replacement_serial_number_id' => null,
                'extra_warranty'            => null,
                'notes'                     => 'Sửa mainboard trong bảo hành',
            ],
            [
                'return_form_id'            => 90004,
                'product_id'                => 4,
                'quantity'                  => 1,
                'serial_number_id'          => 3002,
                'replacement_code'          => null,
                'replacement_serial_number_id' => null,
                'extra_warranty'            => 12,
                'notes'                     => 'Mua thêm gói bảo hành mở rộng 1 năm',
            ],
        ]);
    }
}