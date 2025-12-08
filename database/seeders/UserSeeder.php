<?php

namespace Database\Seeders;

use App\Models\Customers;
use App\Models\Product;
use App\Models\Providers;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = [
            ['name' => 'Nguyễn Văn Thiên', 'email' => 'thiennv@thienphattien.com', 'phone' => '0908 779 167', 'role' => 'Admin'],
            ['name' => 'Nguyễn Đình Thành', 'email' => 'thanhnd@thienphattien.com', 'phone' => '0914 994 997', 'role' => 'Admin'],
            ['name' => 'Đoàn Thanh Trang', 'email' => 'trangdt@thienphattien.com', 'phone' => '0911 788 488', 'role' => 'Admin'],
            ['name' => 'Đoàn Thanh Giang', 'email' => 'giangdt@thienphattien.com', 'phone' => '0915 779 167', 'role' => 'Admin'],
            ['name' => 'Trần Lê Thục Uyên', 'email' => 'thucuyen.tran@thienphattien.com', 'phone' => '0906 146 426', 'role' => 'Admin'],
            ['name' => 'Nguyễn Thị Xuân Hậu', 'email' => 'hauntx@thienphattien.com', 'phone' => '0345 051 482', 'role' => 'Kế toán'],
            ['name' => 'Thạch Hoài Bảo', 'email' => 'bao.thach@thienphattien.com', 'phone' => '0387 823 982', 'role' => 'Quản lý kho'],
            ['name' => 'Phạm Lê Quốc Khởi', 'email' => 'khoi.pham@thienphattien.com', 'phone' => '0386 068 693', 'role' => 'Quản lý kho'],
            ['name' => 'Huỳnh Lê Thiên Phúc', 'email' => 'phuc.huynh@thienphattien.com', 'phone' => '0983 468 473', 'role' => 'Bảo hành'],
            ['name' => 'Phan Thành Nhân', 'email' => 'nhanpt@thienphattien.com', 'phone' => '0867 551 488', 'role' => 'Bảo hành'],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'phone' => $userData['phone'],
                'password' => Hash::make($userData['email']),
            ]);

            if (!empty($userData['role'])) {
                $role = Role::firstOrCreate(['name' => $userData['role']]);
                $user->assignRole($role);
            }
        }
    
        // Customers::create([
        //     'customer_code' => 'kh1',
        //     'customer_name' => 'kh1',
        // ]);
        // Providers::create([
        //     'provider_code' => 'ncc1',
        //     'provider_name' => 'ncc1',
        // ]);
        // Product::insert([
        //     [
        //         'product_code' => 'sp1',
        //         'product_name' => 'sp1',
        //     ],
        //     [
        //         'product_code' => 'sp2',
        //         'product_name' => 'sp2',
        //     ],
        // ]);
        // // Tạo người dùng mặc định
        // $admin = User::create([
        //     'name' => 'Admin User',
        //     'email' => 'admin@thienphattien.com',
        //     'password' => Hash::make('Admin@123'),
        // ]);
        // $admin->assignRole('Admin');

        // $warehouseManager = User::create([
        //     'name' => 'Quản lý kho',
        //     'email' => 'quankho@thienphattien.com',
        //     'password' => Hash::make('Quankho@123'),
        // ]);
        // $warehouseManager->assignRole('Quản lý kho');

        // $serviceUser = User::create([
        //     'name' => 'Bảo hành',
        //     'email' => 'baohanh@thienphattien.com',
        //     'password' => Hash::make('Baohanh@123'),
        // ]);
        // $serviceUser->assignRole('Bảo hành');

        // $serviceUser = User::create([
        //     'name' => 'Kế toán',
        //     'email' => 'ketoan@thienphattien.com',
        //     'password' => Hash::make('Ketoan@123'),
        // ]);
        // $serviceUser->assignRole('Kế toán');
    }
}
