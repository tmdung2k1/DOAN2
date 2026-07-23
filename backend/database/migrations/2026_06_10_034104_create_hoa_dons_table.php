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
        Schema::create('hoa_don', function (Blueprint $table) {
            $table->id('Ma_HoaDon');
            $table->string('ma_hoa_don', 30)->unique();
            $table->unsignedBigInteger('Ma_HopDong');
            $table->foreign('Ma_HopDong')->references('Ma_HopDong')->on('hop_dong');
            $table->date('thang_thanh_toan');
            $table->decimal('tong_tien', 14, 2);
            $table->date('han_chot');
            $table->date('ngay_thanh_toan')->nullable();
            $table->enum('trang_thai', ['chua_thanh_toan', 'da_thanh_toan', 'qua_han'])->default('chua_thanh_toan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoa_don');
    }
};
