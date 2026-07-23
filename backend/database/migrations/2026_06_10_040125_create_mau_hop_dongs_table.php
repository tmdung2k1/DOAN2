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
        Schema::create('mau_hop_dong', function (Blueprint $table) {
            $table->id('Ma_MauHopDong');
            $table->string('ten_mau', 200); // VD: Mẫu hợp đồng chuẩn 2026
            $table->longText('noi_dung_html'); // Lưu cấu trúc HTML để xuất ra trang in hoặc PDF
            $table->boolean('la_mac_dinh')->default(0); // Đánh dấu mẫu nào đang được dùng chính
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mau_hop_dong');
    }
};
