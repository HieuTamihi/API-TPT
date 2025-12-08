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
        Schema::create('product_returns', function (Blueprint $table) {
            $table->id(); // Primary key, auto-increment
            $table->unsignedBigInteger('return_form_id')->comment('ID phiếu trả hàng');
            $table->unsignedBigInteger('product_id')->comment('ID hàng hóa');
            $table->integer('quantity')->comment('Số lượng');
            $table->unsignedBigInteger('serial_number_id')->nullable()->comment('ID số serial');
            $table->unsignedBigInteger('replacement_code')->nullable()->comment('ID SP đổi');
            $table->unsignedBigInteger('replacement_serial_number_id')->nullable()->comment('ID số serial hàng đổi');
            $table->integer('extra_warranty')->nullable()->comment('Bảo hành thêm (tháng)');
            $table->text('notes')->nullable()->comment('Ghi chú');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_returns');
    }
};
