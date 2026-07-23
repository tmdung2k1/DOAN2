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
        Schema::create('dat_phong', function (Blueprint $table) {
            $table->id('Ma_DatPhong');
            $table->string('ma_dat_phong', 20)->unique();
            $table->unsignedBigInteger('Ma_Phong');
            $table->foreign('Ma_Phong')->references('Ma_Phong')->on('phong');
            $table->unsignedBigInteger('Ma_TaiKhoan')->nullable();
            $table->foreign('Ma_TaiKhoan')->references('Ma_TaiKhoan')->on('tai_khoans')->onDelete('restrict');
            $table->string('ho_ten', 100);
            $table->string('so_dien_thoai', 20);
            $table->string('email', 100)->nullable();
            $table->date('ngay_du_kien_den');
            $table->decimal('tien_coc', 14, 2);
            $table->text('ghi_chu')->nullable();
            $table->enum('trang_thai', ['cho_xac_nhan', 'da_coc', 'da_nhan_phong', 'huy'])->default('cho_xac_nhan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dat_phong');
    }
};
