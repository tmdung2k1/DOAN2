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
        Schema::create('phong', function (Blueprint $table) {
            $table->id('Ma_Phong');
            $table->unsignedBigInteger('Ma_Tang')->nullable();
            $table->foreign('Ma_Tang')->references('Ma_Tang')->on('tang')->nullOnDelete();
            $table->unsignedBigInteger('Ma_LoaiPhong');
            $table->foreign('Ma_LoaiPhong')->references('Ma_LoaiPhong')->on('loai_phong');
            $table->string('so_phong', 20);
            $table->decimal('dien_tich', 6, 2);
            $table->decimal('chieu_dai', 5, 2)->nullable();
            $table->decimal('chieu_rong', 5, 2)->nullable();
            $table->decimal('gia_thue', 12, 2);
            $table->decimal('gia_coc', 12, 2)->default(0);
            $table->enum('trang_thai', ['trong', 'da_thue', 'dat_truoc', 'bao_tri'])->default('trong');
            $table->string('link_3d', 500)->nullable(); // Tích hợp xem phòng 3D
            $table->text('mo_ta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phong');
    }
};
