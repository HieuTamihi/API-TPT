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
        Schema::create('inventory_lookup', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('sn_id');
            $table->integer('provider_id');
            $table->dateTime('import_date');
            $table->integer('storage_duration')->comment('Thời gian tồn');
            $table->integer('status');
            $table->dateTime('warranty_date')->nullable()->comment('Ngày bảo hành');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_lookup');
    }
};
