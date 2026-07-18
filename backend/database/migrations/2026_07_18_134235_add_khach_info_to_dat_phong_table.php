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
        Schema::table('dat_phong', function (Blueprint $table) {
            // Cho phép khach_id = null (khách vãng lai không cần tài khoản)
            $table->unsignedBigInteger('khach_id')->nullable()->change();

            // Thêm thông tin khách trực tiếp
            $table->string('ho_ten', 100)->after('khach_id');
            $table->string('so_dien_thoai', 20)->after('ho_ten');
            $table->string('email', 100)->nullable()->after('so_dien_thoai');
            $table->text('ghi_chu')->nullable()->after('tien_coc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dat_phong', function (Blueprint $table) {
            $table->dropColumn(['ho_ten', 'so_dien_thoai', 'email', 'ghi_chu']);
        });
    }
};
