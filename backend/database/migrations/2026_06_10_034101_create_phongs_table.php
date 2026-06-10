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
            $table->id();
            $table->foreignId('tang_id')->nullable()->constrained('tang')->nullOnDelete();
            $table->foreignId('loai_phong_id')->constrained('loai_phong');
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
