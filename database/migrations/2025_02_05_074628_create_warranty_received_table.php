<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('warranty_received', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_received_id')->nullable();
            $table->unsignedBigInteger('product_return_id')->nullable();
            $table->string('name_warranty');
            $table->string('state_recei')->nullable();
            $table->text('note')->nullable();
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('warranty_received');
    }
};
