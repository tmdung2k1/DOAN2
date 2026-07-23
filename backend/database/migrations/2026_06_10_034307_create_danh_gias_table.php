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
        Schema::create('danh_gia', function (Blueprint $table) {
            $table->id('Ma_DanhGia');
            $table->unsignedBigInteger('Ma_Phong');
            $table->foreign('Ma_Phong')->references('Ma_Phong')->on('phong');
            $table->unsignedBigInteger('Ma_TaiKhoan');
            $table->foreign('Ma_TaiKhoan')->references('Ma_TaiKhoan')->on('tai_khoans')->onDelete('restrict');
            $table->unsignedTinyInteger('diem_danh_gia');
            $table->text('binh_luan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::dropIfExists('danh_gia');
    }
};
