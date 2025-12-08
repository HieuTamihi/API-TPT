<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo vai trò
        $adminRole = Role::create(['name' => 'Admin']);
        $warehouseRole = Role::create(['name' => 'Quản lý kho']);
        $serviceRole = Role::create(['name' => 'Bảo hành']);
        $accountantRole = Role::create(['name' => 'Kế toán']);

        // Danh sách quyền
        $permissions = [
            'admin',
            'quankho',
            'dichvu',
            'ketoan',
        ];
        // Tạo quyền và gán quyền vào vai trò
        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);

            if ($permissionName === 'admin') {
                // Gán quyền 'admin' cho vai trò Admin
                $adminRole->givePermissionTo($permission);
            } elseif ($permissionName === 'quankho') {
                // Gán quyền 'quankho' cho vai trò Quản lý kho
                $warehouseRole->givePermissionTo($permission);
            } elseif ($permissionName === 'dichvu') {
                // Gán quyền 'dichvu' cho vai trò Bảo hành
                $serviceRole->givePermissionTo($permission);
            } elseif ($permissionName === 'ketoan') {
                // Gán quyền 'ketoan' cho vai trò kế toán
                $accountantRole->givePermissionTo($permission);
            }
        }
    }
}
