<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            ['group_id' => 1, 'provider_code' => 'NCC001', 'provider_name' => 'Công ty TNHH FPT Shop',        'address' => '260 Nguyễn Văn Linh, Q.7, TP.HCM', 'phone' => '19006600', 'email' => 'cskh@fptshop.com.vn'],
            ['group_id' => 1, 'provider_code' => 'NCC002', 'provider_name' => 'Thế Giới Di Động',           'address' => '222 Lê Duẩn, Q.1, TP.HCM',       'phone' => '18001060', 'email' => 'cskh@thegioididong.com'],
            ['group_id' => 1, 'provider_code' => 'NCC003', 'provider_name' => 'CellphoneS',                 'address' => '117 Lý Nam Đế, Hà Nội',          'phone' => '18002097'],
            ['group_id' => 2, 'provider_code' => 'NCC004', 'provider_name' => 'Apple Việt Nam (Synnex FPT)', 'address' => 'Tầng 15, Tòa nhà Viettel, Hà Nội', 'phone' => '18001123'],
            ['group_id' => 2, 'provider_code' => 'NCC005', 'provider_name' => 'Samsung Electronics VN',      'address' => 'Lô CN6, KCN Yên Phong, Bắc Ninh', 'phone' => '1800588889'],
            ['group_id' => 3, 'provider_code' => 'NCC006', 'provider_name' => 'Công ty Sony Việt Nam',       'address' => 'Tầng 12, Tòa nhà Keangnam, HN',   'phone' => '1800588851'],
            ['group_id' => 1, 'provider_code' => 'NCC007', 'provider_name' => 'Hoàng Hà Mobile',            'address' => '260 Cầu Giấy, Hà Nội', 'phone' => '19002098'],
            ['group_id' => 1, 'provider_code' => 'NCC008', 'provider_name' => 'Di Động Việt',               'address' => '192 Trần Phú, Q.5, TP.HCM',       'phone' => '180011122'],
            ['group_id' => 1, 'provider_code' => 'NCC009', 'provider_name' => 'Viettel Store',               'address' => 'Tầng 1, Tòa nhà Viettel, HN',     'phone' => '18008098'],
            ['group_id' => 1, 'provider_code' => 'NCC010', 'provider_name' => 'Phong Vũ Computer',          'address' => '264 Nguyễn Thị Minh Khai, Q.3',   'phone' => '18006866'],
        ];

        foreach ($providers as $p) {
            DB::table('providers')->updateOrInsert(
                ['provider_code' => $p['provider_code']],
                $p + ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}