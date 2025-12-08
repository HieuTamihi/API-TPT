<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id')->comment('ID quotations');
            $table->string('service_name')->comment('Tên');
            $table->string('unit')->nullable()->comment('Đơn vị tính');
            $table->string('brand')->nullable()->comment('Hãng');
            $table->integer('quantity')->default(1)->comment('Số lượng');
            $table->decimal('unit_price', 20, 2)->default(0)->comment('Đơn giá');
            $table->decimal('tax_rate', 5, 2)->default(0)->comment('Thuế(%)');
            $table->decimal('total', 20, 2)
                ->comment('Thành tiền (bao gồm thuế)');
            $table->text('note')->nullable()->comment('Ghi chú');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotation_services');
    }
}
