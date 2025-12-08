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
        Schema::create('warehouse_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('code'); // Mã phiếu
            $table->integer('user_id'); // Người tạo phiếu
            $table->integer('from_warehouse_id'); // Kho nguồn
            $table->integer('to_warehouse_id');   // Kho đích
            $table->timestamp('transfer_date')->useCurrent(); // Ngày tạo phiếu
            $table->integer('status')->default('1')->comment("1: Hoàn thành, 0: Hủy"); // Trạng thái phiếu
            $table->text('note')->nullable(); // Ghi chú
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_transfers');
    }
};
