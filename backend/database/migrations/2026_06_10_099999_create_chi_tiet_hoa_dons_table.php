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
        Schema::create('chi_tiet_hoa_don', function (Blueprint $table) {
            $table->id('Ma_ChiTietHoaDon');
            $table->unsignedBigInteger('Ma_HoaDon');
            $table->foreign('Ma_HoaDon')->references('Ma_HoaDon')->on('hoa_don')->onDelete('cascade');
            $table->enum('loai_phi', ['tien_phong', 'tien_dien', 'tien_nuoc', 'rac', 'wifi', 'khac']);
            $table->decimal('so_luong', 10, 2)->default(1);
            $table->decimal('don_gia', 12, 2);
            $table->decimal('thanh_tien', 14, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_hoa_don');
    }
};
