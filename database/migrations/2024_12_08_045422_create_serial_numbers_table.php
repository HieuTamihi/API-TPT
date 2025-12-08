<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSerialNumbersTable extends Migration
{
    public function up()
    {
        Schema::create('serial_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('serial_code', 100)->unique();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('status')
                ->default(1)
                ->comment('1: Nhập hàng, 2: Xuất hàng, 3: Tiếp nhận, 4: Trả hàng, 5: Đang mượn');
            $table->string('note')->nullable();
            $table->integer('warehouse_id')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('serial_numbers');
    }
}
