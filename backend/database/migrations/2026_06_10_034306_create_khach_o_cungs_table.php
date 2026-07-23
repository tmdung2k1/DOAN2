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
        // Tạo bảng khach_hang trực tiếp (gộp từ khach_o_cung + rename + add dia_chi)
        Schema::create('khach_hang', function (Blueprint $table) {
            $table->id('Ma_KhachHang');
            $table->unsignedBigInteger('Ma_HopDong');
            $table->foreign('Ma_HopDong')->references('Ma_HopDong')->on('hop_dong')->onDelete('cascade');
            $table->string('ho_ten', 100);
            $table->string('cccd', 20)->nullable();
            $table->string('so_dien_thoai', 20)->nullable();
            $table->string('dia_chi', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khach_hang');
    }
};
