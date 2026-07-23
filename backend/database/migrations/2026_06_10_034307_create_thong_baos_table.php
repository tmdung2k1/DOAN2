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
        Schema::create('thong_bao', function (Blueprint $table) {
            $table->id('Ma_ThongBao');
            $table->unsignedBigInteger('Ma_TaiKhoan');
            $table->foreign('Ma_TaiKhoan')->references('Ma_TaiKhoan')->on('tai_khoans')->onDelete('cascade');
            $table->string('tieu_de', 200);
            $table->text('noi_dung');
            $table->boolean('da_doc')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thong_bao');
    }
};
