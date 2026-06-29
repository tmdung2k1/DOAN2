<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
      
        Schema::disableForeignKeyConstraints();

        Schema::table('dat_phong', function (Blueprint $table) {
            $table->dropForeign('dat_phong_khach_id_foreign');
            $table->foreign('khach_id')
                  ->references('id')
                  ->on('tai_khoans')
                  ->onDelete('restrict');
        });

        Schema::table('danh_gia', function (Blueprint $table) {
            $table->dropForeign('danh_gia_khach_id_foreign');
            $table->foreign('khach_id')
                  ->references('id')
                  ->on('tai_khoans')
                  ->onDelete('restrict');
        });

        Schema::table('thong_bao', function (Blueprint $table) {
            $table->dropForeign('thong_bao_tai_khoan_id_foreign');
            $table->foreign('tai_khoan_id')
                  ->references('id')
                  ->on('tai_khoans')
                  ->onDelete('cascade');
        });

        Schema::table('tin_nhan_ai', function (Blueprint $table) {
            $table->dropForeign('tin_nhan_ai_tai_khoan_id_foreign');
            $table->foreign('tai_khoan_id')
                  ->references('id')
                  ->on('tai_khoans')
                  ->onDelete('cascade');
        });

        Schema::table('yeu_cau_bao_tri', function (Blueprint $table) {
            $table->dropForeign('yeu_cau_bao_tri_khach_id_foreign');
            $table->foreign('khach_id')
                  ->references('id')
                  ->on('tai_khoans')
                  ->onDelete('restrict');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('dat_phong', function (Blueprint $table) {
            $table->dropForeign(['khach_id']);
            $table->foreign('khach_id')->references('id')->on('tai_khoan');
        });

        Schema::table('danh_gia', function (Blueprint $table) {
            $table->dropForeign(['khach_id']);
            $table->foreign('khach_id')->references('id')->on('tai_khoan');
        });

        Schema::table('thong_bao', function (Blueprint $table) {
            $table->dropForeign(['tai_khoan_id']);
            $table->foreign('tai_khoan_id')->references('id')->on('tai_khoan')->onDelete('cascade');
        });

        Schema::table('tin_nhan_ai', function (Blueprint $table) {
            $table->dropForeign(['tai_khoan_id']);
            $table->foreign('tai_khoan_id')->references('id')->on('tai_khoan')->onDelete('cascade');
        });

        Schema::table('yeu_cau_bao_tri', function (Blueprint $table) {
            $table->dropForeign(['khach_id']);
            $table->foreign('khach_id')->references('id')->on('tai_khoan');
        });

        Schema::enableForeignKeyConstraints();
    }
};
