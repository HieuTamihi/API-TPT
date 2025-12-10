<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['group_id' => 1, 'product_code' => 'LAP001', 'product_name' => 'Laptop Dell Inspiron 15 3511', 'brand' => 'Dell', 'warranty' => 24],
            ['group_id' => 1, 'product_code' => 'LAP002', 'product_name' => 'MacBook Air M2 2022', 'brand' => 'Apple', 'warranty' => 12],
            ['group_id' => 2, 'product_code' => 'PHN001', 'product_name' => 'iPhone 15 Pro Max', 'brand' => 'Apple', 'warranty' => 12],
            ['group_id' => 2, 'product_code' => 'PHN002', 'product_name' => 'Samsung Galaxy S24 Ultra', 'brand' => 'Samsung', 'warranty' => 12],
            ['group_id' => 3, 'product_code' => 'TV001', 'product_name' => 'Smart TV Sony 55 inch 4K', 'brand' => 'Sony', 'warranty' => 36],
        ];

        foreach ($products as $p) {
            DB::table('products')->updateOrInsert(
                ['product_code' => $p['product_code']],
                $p
            );
        }
    }
}
