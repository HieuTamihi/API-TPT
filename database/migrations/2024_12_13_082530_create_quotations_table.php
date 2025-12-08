<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id(); // ID tự tăng
            $table->unsignedBigInteger('reception_id')->comment('ID phiếu tiếp nhận');
            $table->string('quotation_code')->unique()->comment('Mã phiếu');
            $table->unsignedBigInteger('customer_id')->comment('ID khách hàng');
            $table->string('address')->nullable()->comment('Địa chỉ khách hàng');
            $table->date('quotation_date')->comment('Ngày lập phiếu');
            $table->string('contact_person')->nullable()->comment('Người liên hệ');
            $table->text('notes')->nullable()->comment('Ghi chú');
            $table->unsignedBigInteger('user_id')->comment('Người lập phiếu');
            $table->string('contact_phone')->nullable()->comment('Số điện thoại liên hệ');
            $table->decimal('total_amount', 20, 2)->comment('Tổng tiền');
            $table->timestamps(); // Tự động thêm cột created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
