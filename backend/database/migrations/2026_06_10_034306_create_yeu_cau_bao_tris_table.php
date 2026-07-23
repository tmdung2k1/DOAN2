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
        Schema::create('yeu_cau_bao_tri', function (Blueprint $table) {
            $table->id('Ma_YeuCauBaoTri');
            $table->unsignedBigInteger('Ma_Phong');
            $table->foreign('Ma_Phong')->references('Ma_Phong')->on('phong');
            $table->unsignedBigInteger('Ma_TaiKhoan');
            $table->foreign('Ma_TaiKhoan')->references('Ma_TaiKhoan')->on('tai_khoans')->onDelete('restrict');
            $table->string('tieu_de', 200);
            $table->text('noi_dung');
            $table->enum('trang_thai', ['cho_xu_ly', 'dang_sua', 'hoan_thanh'])->default('cho_xu_ly');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yeu_cau_bao_tri');
    }
};
