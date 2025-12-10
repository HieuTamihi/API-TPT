<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            // 1–5: Khách lẻ thường
            ['group_id' => 1, 'customer_code' => 'KH00001',  'customer_name' => 'Nguyễn Văn An',         'phone' => '0909123456', 'email' => 'an.nguyen@gmail.com',     'address' => '123 Lê Lợi, Q.1, TP.HCM',            'note' => 'Khách quen, hay mua iPhone'],
            ['group_id' => 1, 'customer_code' => 'KH00002',  'customer_name' => 'Trần Thị Bé',          'phone' => '0918234567', 'email' => null,                              'address' => '45 Nguyễn Trãi, Q.5, TP.HCM',        'note' => 'Mua MacBook trả góp'],
            ['group_id' => 1, 'customer_code' => 'KH00003',  'customer_name' => 'Lê Hoàng Cường',       'phone' => '0388123456', 'email' => 'cuong.le@hotmail.com',            'address' => '78 Trần Hưng Đạo, Hà Nội',           'note' => 'Yêu cầu bảo hành nhanh'],
            ['group_id' => 1, 'customer_code' => 'KH00004',  'customer_name' => 'Phạm Minh Đức',        'phone' => '0935123456', 'email' => null,                              'address' => '56 Nguyễn Huệ, Đà Nẵng',           'note' => 'Thích màu Titan Black'],
            ['group_id' => 1, 'customer_code' => 'KH00005',  'customer_name' => 'Vũ Thị Hương',         'phone' => '0977234567', 'email' => 'huongvu90@yahoo.com',              'address' => '12 Hàng Bông, Hà Nội',                'note' => 'Mua tặng sinh nhật chồng'],

            // 6–10: Khách VIP / Mua nhiều lần
            ['group_id' => 2, 'customer_code' => 'VIP001',   'customer_name' => 'Đỗ Quang Huy (VIP)',   'phone' => '0913888999', 'email' => 'huy.dq@vip.com',                  'address' => 'Penthouse Landmark 81, TP.HCM',      'note' => 'VIP – Đã mua > 500 triệu'],
            ['group_id' => 2, 'customer_code' => 'VIP002',   'customer_name' => 'Nguyễn Ngọc Lan Anh',  'phone' => '0905111222', 'email' => 'lananh.ceo@gmail.com',            'address' => 'Villa Ecopark, Hà Nội',              'note' => 'CEO, mua quà đối tác'],
            ['group_id' => 2, 'customer_code' => 'VIP003',   'customer_name' => 'Trần Quốc Bảo',        'phone' => '0988111333', 'email' => null,                              'address' => 'Khu đô thị Sala, Q.2, TP.HCM',        'note' => 'Mua 3 MacBook Pro M3 Max cùng lúc'],

            // 11–15: Khách hàng doanh nghiệp / công ty
            ['group_id' => 3, 'customer_code' => 'CTY001',   'customer_name' => 'Công ty CP Công nghệ ABC', 'phone' => '02873009999', 'email' => 'purchase@abc.com.vn',        'address' => 'Tầng 15, Bitexco, Q.1, TP.HCM',       'tax_code' => '0312345678', 'note' => 'Mua số lượng lớn laptop văn phòng'],
            ['group_id' => 3, 'customer_code' => 'CTY002',   'customer_name' => 'Tập đoàn XYZ',            'phone' => '02473008888', 'email' => 'it@xyzgroup.vn',              'address' => 'Tòa nhà Keangnam, Hà Nội',            'tax_code' => '0101234567', 'note' => 'Đối tác chiến lược'],
            ['group_id' => 3, 'customer_code' => 'CTY003',   'customer_name' => 'Trường THPT Chuyên Hà Nội - Amsterdam', 'phone' => '0243834567', 'email' => null,                    'address' => 'Hoàng Minh Giám, Cầu Giấy, HN',       'tax_code' => null,         'note' => 'Mua iMac cho phòng tin học'],

            // 16–20: Khách sỉ / đại lý nhỏ
            ['group_id' => 4, 'customer_code' => 'SI001',     'customer_name' => 'Cửa hàng Di Động Minh Phát', 'phone' => '0938123123', 'email' => 'minhphat.mobile@gmail.com', 'address' => 'Chợ Tân Bình, TP.HCM',               'note' => 'Đại lý cấp 2 – lấy sỉ iPhone'],
            ['group_id' => 4, 'customer_code' => 'SI002',     'customer_name' => 'Shop Laptop Cũ 24h',         'phone' => '0975123456', 'email' => null,                         'address' => '123 Phạm Văn Đồng, Hà Nội',         'note' => 'Chuyên thu cũ đổi mới'],
            ['group_id' => 4, 'customer_code' => 'SI003',     'customer_name' => 'Điện thoại Joy – Đà Nẵng',    'phone' => '0905123456', 'email' => 'joyphone@gmail.com',         'address' => '45 Lê Duẩn, Đà Nẵng',                 'note' => 'Đại lý Samsung chính hãng'],

            // Khách đặc biệt
            ['group_id' => 1, 'customer_code' => 'KH99999',   'customer_name' => 'Nguyễn Thị Kim Ngân',        'phone' => '0914111222', 'email' => null,                         'address' => null,                                          'note' => 'Khách lâu năm, hay mua quà biếu'],
            ['group_id' => 1, 'customer_code' => 'KH88888',   'customer_name' => 'Trần Văn Tèo (Test)',         'phone' => '0123456789', 'email' => 'test@gmail.com',             'address' => '123 Đường Test, Q.Test',                      'note' => 'Dùng để test hệ thống'],
        ];

        foreach ($customers as $customer) {
            DB::table('customers')->updateOrInsert(
                ['customer_code' => $customer['customer_code']], // tránh trùng
                $customer + [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}