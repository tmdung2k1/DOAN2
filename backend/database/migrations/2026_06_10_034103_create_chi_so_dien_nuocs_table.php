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
        Schema::create('chi_so_dien_nuoc', function (Blueprint $table) {
            $table->id('Ma_ChiSoDienNuoc');
            $table->unsignedBigInteger('Ma_Phong');
            $table->foreign('Ma_Phong')->references('Ma_Phong')->on('phong');
            $table->date('thang_ghi_nhan'); // Lưu ngày đầu tháng, ví dụ: 2026-06-01
            $table->enum('loai_chi_so', ['dien', 'nuoc']);
            $table->decimal('chi_so_cu', 10, 2)->default(0);
            $table->decimal('chi_so_moi', 10, 2);
            $table->decimal('don_gia', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_so_dien_nuoc');
    }
};
