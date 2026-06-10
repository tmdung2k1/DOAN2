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
        Schema::create('tai_khoan', function (Blueprint $table) {
            $table->id();
            // Thiết kế 1 tài khoản Admin duy nhất theo yêu cầu dự án
            $table->boolean('la_admin')->default(0);
            $table->string('ho_ten', 100);
            $table->string('email', 150)->unique();
            $table->string('so_dien_thoai', 20)->nullable();
            $table->string('mat_khau')->nullable();
            $table->string('cccd', 20)->unique()->nullable();
            $table->string('anh_cccd_truoc', 500)->nullable();
            $table->string('anh_cccd_sau', 500)->nullable();
            $table->text('dia_chi')->nullable();
            $table->boolean('trang_thai')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tai_khoan');
    }
};
