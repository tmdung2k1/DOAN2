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
        Schema::create('hop_dong', function (Blueprint $table) {
            $table->id();
            $table->string('ma_hop_dong', 30)->unique();
            $table->foreignId('phong_id')->constrained('phong');
            $table->foreignId('khach_id')->constrained('tai_khoan');
            $table->date('ngay_bat_dau');
            $table->date('ngay_ket_thuc');
            $table->decimal('gia_thue_hang_thang', 12, 2);
            $table->decimal('tien_coc', 12, 2);
            $table->unsignedTinyInteger('ngay_thu_tien_hang_thang')->default(5);
            $table->enum('trang_thai', ['hieu_luc', 'het_han', 'da_huy'])->default('hieu_luc');
            $table->string('file_hop_dong_pdf', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hop_dong');
    }
};
