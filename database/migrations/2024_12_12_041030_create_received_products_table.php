<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivedProductsTable extends Migration
{
    public function up()
    {
        Schema::create('received_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reception_id')->comment('ID phiếu tiếp nhận');
            $table->unsignedBigInteger('product_id')->comment('ID hàng hóa');
            $table->integer('quantity')->comment('Số lượng');
            $table->unsignedBigInteger('serial_id')->nullable()->comment('Serial Id');
            $table->text('status')->nullable()->comment('Tình trạng tiếp nhận');
            $table->text('note')->nullable()->comment('Ghi chú');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('received_products');
    }
}
