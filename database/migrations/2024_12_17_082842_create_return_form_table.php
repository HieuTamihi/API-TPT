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
        Schema::create('return_form', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reception_id')->comment('Id tiếp nhận');
            $table->string('return_code')->unique()->comment('Mã phiếu');
            $table->unsignedBigInteger('customer_id')->comment('ID khách hàng');
            $table->string('address')->nullable()->comment('Địa chỉ');
            $table->date('date_created')->comment('Ngày lập phiếu');
            $table->string('contact_person')->nullable()->comment('Người liên h');
            $table->string('return_method')->comment('Phương thức trả hàng');
            $table->unsignedBigInteger('user_id')->comment('Người lập phiếu');
            $table->string('phone_number')->nullable()->comment('Số điện thoại');
            $table->text('notes')->nullable()->comment('Ghi');
            $table->string('status')->default(1)->comment('1: Hoàn thành, 2 Khách không đồng ý');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_form');
    }
};
