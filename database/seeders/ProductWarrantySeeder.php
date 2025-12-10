<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductWarrantySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_warranties')->insertOrIgnore([
            ['product_id' => 1, 'info' => 'Bought at FPT Shop - Invoice 001234', 'warranty' => 24, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['product_id' => 1, 'info' => 'Bought online - Shopee order 240615001', 'warranty' => 24, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['product_id' => 3, 'info' => 'Apple Care+ included', 'warranty' => 24, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['product_id' => 4, 'info' => 'Samsung Care+ 2 years', 'warranty' => 24, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['product_id' => 5, 'info' => 'Warranty extended to 48 months', 'warranty' => 48, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}