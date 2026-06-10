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
            $table->id();
            $table->string('ma_dat_phong', 20)->unique();
            $table->foreignId('phong_id')->constrained('phong');
            $table->foreignId('khach_id')->constrained('tai_khoan');
            $table->date('ngay_du_kien_den');
            $table->decimal('tien_coc', 14, 2);
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
