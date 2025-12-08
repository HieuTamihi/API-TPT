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
        Schema::create('warranty_history', function (Blueprint $table) {
            $table->id();
            $table->integer('warranty_lookup_id')->comment("id tra cứu bảo hành")->nullable();
            $table->integer('receiving_id')->comment("id phiếu tiếp nhận")->nullable();
            $table->integer('return_id')->comment("id phiếu trả hàng")->nullable();
            $table->integer('product_return_id')->comment("id sản phẩm trả")->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranty_history');
    }
};
