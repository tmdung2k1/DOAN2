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
        Schema::create('cai_dat', function (Blueprint $table) {
            $table->id('Ma_CaiDat');
            $table->integer('gia_dien')->default(3500); // Giá điện (VNĐ/kWh)
            $table->integer('gia_nuoc')->default(15000); // Giá nước (VNĐ/khối)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cai_dat');
    }
};
