<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReceivingSeeder extends Seeder
{
    public function run()
    {
        // 1. Lấy ID user và customer có sẵn (hoặc đặt mặc định là 1 nếu bạn lười tạo)
        // Đảm bảo trong DB bạn đã có User id=1 và Customer id=1 nhé
        $userId = DB::table('users')->value('id') ?? 1;
        $customerId = DB::table('customers')->value('id') ?? 1;

        // 2. Tạo dữ liệu cho bảng Products (Cần thiết để API hiện tên sản phẩm)
        $productId1 = DB::table('products')->insertGetId([
            'product_code' => 'IPHONE15',
            'product_name' => 'iPhone 15 Pro Max',
            'created_at' => now(), 'updated_at' => now()
        ]);
        
        $productId2 = DB::table('products')->insertGetId([
            'product_code' => 'SAMSUNG_S24',
            'product_name' => 'Samsung Galaxy S24',
            'created_at' => now(), 'updated_at' => now()
        ]);

        // 3. Tạo dữ liệu Bảng Receiving (Phiếu tiếp nhận)
        $receivings = [
            [
                'id' => 1,
                'branch_id' => 1,
                'form_type' => 1,
                'form_code_receiving' => 'PN000001',
                'customer_id' => $customerId,
                'address' => '123 Đường Test, Hà Nội',
                'date_created' => Carbon::now(),
                'contact_person' => 'Nguyễn Văn Test',
                'notes' => 'Máy bị vỡ màn hình',
                'user_id' => $userId,
                'phone' => '0901234567',
                'closed_at' => null, // Chưa đóng
                'status' => 1, // Đang xử lý
                'state' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'branch_id' => 1,
                'form_type' => 2,
                'form_code_receiving' => 'PN000002',
                'customer_id' => $customerId,
                'address' => '456 Đường Demo, TP.HCM',
                'date_created' => Carbon::now()->subDays(2),
                'contact_person' => 'Trần Thị Demo',
                'notes' => 'Máy không lên nguồn, khách cần gấp',
                'user_id' => $userId,
                'phone' => '0987654321',
                'closed_at' => Carbon::now(), // Đã đóng
                'status' => 2, // Hoàn thành
                'state' => 1,
                'created_at' => now()->subDays(2),
                'updated_at' => now(),
            ]
        ];

        DB::table('receiving')->insert($receivings);

        // 4. Tạo Serial (để test search serial)
        $serialId1 = DB::table('serial_numbers')->insertGetId([
            'serial_code' => 'SN111111', 'product_id' => $productId1, 'created_at' => now(), 'updated_at' => now()
        ]);
        $serialId2 = DB::table('serial_numbers')->insertGetId([
            'serial_code' => 'SN222222', 'product_id' => $productId2, 'created_at' => now(), 'updated_at' => now()
        ]);

        // 5. Gán sản phẩm vào phiếu (received_products)
        DB::table('received_products')->insert([
            // Phiếu 1 nhận iPhone
            [
                'reception_id' => 1,
                'product_id' => $productId1,
                'serial_id' => $serialId1,
                'quantity' => 1,
                'status' => 1,
                'created_at' => now(), 'updated_at' => now()
            ],
            // Phiếu 2 nhận Samsung
            [
                'reception_id' => 2,
                'product_id' => $productId2,
                'serial_id' => $serialId2,
                'quantity' => 1,
                'status' => 1,
                'created_at' => now(), 'updated_at' => now()
            ]
        ]);
    }
}