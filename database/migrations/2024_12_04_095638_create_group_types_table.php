<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('group_types', function (Blueprint $table) {
            $table->id();
            $table->string('group_name')->nullable();
            $table->timestamps();
        });
        
        //import data group_types
        DB::table('group_types')->insert([
            [
                'id' => 1,
                'group_name' => 'Khách hàng',
            ],
            [
                'id' => 2,
                'group_name' => 'Nhà cung cấp',
            ],
            [
                'id' => 3,
                'group_name' => 'Hàng hóa',
            ],
            [
                'id' => 4,
                'group_name' => 'Nhân viên',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loai_nhom');
    }
};
