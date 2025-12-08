<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Update table imports
        Schema::table('imports', function (Blueprint $table) {
            $table->integer('warehouse_id')->default(1);
        });

        // Update table inventory_lookup

        Schema::table('exports', function (Blueprint $table) {
            $table->integer('warehouse_id')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('imports', function (Blueprint $table) {
            $table->dropColumn('warehouse_id'); // Xóa cột warehouse_id
        });
        // Revert changes to inventory_lookup table
        Schema::table('exports', function (Blueprint $table) {
            $table->dropColumn('warehouse_id'); // Xóa cột warehouse_id
        });
    }
};
