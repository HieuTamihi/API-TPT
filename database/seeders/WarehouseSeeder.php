<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('warehouses')->insert([
            [
                'id' => 1,
                'type' => '1',
                'warehouse_code' => 'KHM',
                'warehouse_name' => 'Kho Hàng Mới',
            ],
            [
                'id' => 2,
                'type' => '2',
                'warehouse_code' => 'KBH',
                'warehouse_name' => 'Kho Bảo Hành',
            ],
        ]);
    }
}
