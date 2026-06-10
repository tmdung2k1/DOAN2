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
        Schema::create('thong_ke_doanh_thu', function (Blueprint $table) {
            $table->id();
            $table->date('thang_nam'); // Lưu ngày mùng 1 của tháng để dễ lọc (VD: 2026-06-01)
            $table->decimal('tong_thu', 14, 2)->default(0); // Tiền thu từ hóa đơn
            $table->decimal('tong_chi', 14, 2)->default(0); // Tiền chi cho bảo trì, v.v.
            $table->decimal('loi_nhuan', 14, 2)->default(0); // Tổng thu - Tổng chi
            $table->text('ghi_chu')->nullable();
            $table->timestamps();

            // Đảm bảo mỗi tháng chỉ có 1 dòng thống kê duy nhất
            $table->unique('thang_nam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thong_ke_doanh_thu');
    }
};
