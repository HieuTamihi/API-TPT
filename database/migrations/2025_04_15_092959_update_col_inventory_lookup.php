<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventory_lookup', function (Blueprint $table) {
            $table->integer('remaining_quantity')->default(0);
        });
        Schema::table('warranty_lookup', function (Blueprint $table) {
            $table->integer('export_id')->default(0);
        });
        Schema::table('inventory_lookup', function (Blueprint $table) {
            $table->integer('import_id')->default(0);
            $table->integer('warehouse_id')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
