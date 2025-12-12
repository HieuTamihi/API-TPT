<?php

namespace Database\Seeders;

use App\Models\Customers;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(WarehouseSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(ProductWarrantySeeder::class);
        $this->call(ProductReturnSeeder::class);
        $this->call(ProviderSeeder::class);
        $this->call(SerialNumberSeeder::class);
        $this->call(InventoryLookupSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(ReceivingSeeder::class);
        $this->call(SerialNumberSeeder::class);
        $this->call(WarrantyLookupSeeder::class);
        $this->call(QuotationsTableSeeder::class);
    }
}
