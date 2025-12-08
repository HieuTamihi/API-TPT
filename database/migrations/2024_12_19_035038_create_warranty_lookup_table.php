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
        Schema::create('warranty_lookup', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('sn_id');
            $table->integer('customer_id')->nullable();
            $table->string('name_warranty')->nullable();
            $table->dateTime('export_return_date')->nullable();
            $table->integer('warranty')->nullable();
            $table->integer('status')->comment("0: Bảo hành, 1: Dịch vụ, 2: Bảo hành dịch vụ");
            $table->string('name_expire_date')->comment("Thông tin bảo hành thêm")->nullable();
            $table->dateTime('warranty_expire_date')->comment("Ngày hết hạn bảo hành")->nullable();
            $table->integer('warranty_extra')->nullable();
            $table->dateTime('return_date')->comment("Ngày trả hàng")->nullable();
            $table->dateTime('service_warranty_expired')->comment("Ngày hết hạn bảo hành dịch vụ")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranty_lookup');
    }
};
