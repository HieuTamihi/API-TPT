<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class QuotationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Lấy danh sách ID từ các bảng liên quan để tránh lỗi khóa ngoại (Foreign Key)
        // Lưu ý: Đảm bảo các bảng receiving, customers, users đã có dữ liệu trước khi chạy seeder này.
        $receptionIds = DB::table('receiving')->pluck('id')->toArray();
        $customerIds = DB::table('customers')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        // Nếu bảng liên quan chưa có dữ liệu, ta dùng mảng mẫu [1] để test (cần cẩn thận lỗi SQL)
        if (empty($receptionIds)) $receptionIds = [1];
        if (empty($customerIds)) $customerIds = [1];
        if (empty($userIds)) $userIds = [1];

        $limit = 5; // Số lượng bản ghi muốn tạo

        for ($i = 0; $i < $limit; $i++) {
            DB::table('quotations')->insert([
                'reception_id'   => $faker->randomElement($receptionIds),
                'quotation_code' => 'QT-' . strtoupper(Str::random(8)), // VD: QT-A1B2C3D4
                'customer_id'    => $faker->randomElement($customerIds),
                'address'        => $faker->address,
                'quotation_date' => $faker->date('Y-m-d'), // Ngày lập phiếu
                'contact_person' => $faker->name,
                'notes'          => $faker->sentence(10),
                'user_id'        => $faker->randomElement($userIds),
                'contact_phone'  => $faker->phoneNumber,
                'total_amount'   => $faker->randomFloat(2, 100000, 50000000), // Random từ 100k đến 50tr
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}