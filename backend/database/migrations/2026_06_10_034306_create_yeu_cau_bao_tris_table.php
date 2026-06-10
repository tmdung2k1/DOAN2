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
            $table->id();
            $table->foreignId('phong_id')->constrained('phong');
            $table->foreignId('khach_id')->constrained('tai_khoan');
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
