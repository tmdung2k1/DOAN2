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
        Schema::create('thanh_toan', function (Blueprint $table) {
            $table->id('Ma_ThanhToan');
            $table->string('ma_giao_dich', 50)->unique();
            $table->unsignedBigInteger('Ma_HoaDon')->nullable();
            $table->foreign('Ma_HoaDon')->references('Ma_HoaDon')->on('hoa_don');
            $table->unsignedBigInteger('Ma_DatPhong')->nullable();
            $table->foreign('Ma_DatPhong')->references('Ma_DatPhong')->on('dat_phong');
            $table->decimal('so_tien', 14, 2);
            $table->enum('phuong_thuc', ['tien_mat', 'chuyen_khoan', 'paypal']);
            $table->string('paypal_order_id', 100)->nullable();
            $table->enum('trang_thai', ['thanh_cong', 'dang_cho', 'that_bai']);
            $table->timestamp('ngay_thanh_toan')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thanh_toan');
    }
};
