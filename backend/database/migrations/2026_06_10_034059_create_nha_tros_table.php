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
        Schema::create('nha_tro', function (Blueprint $table) {
            $table->id();
            $table->string('ten_nha_tro', 200);
            $table->string('dia_chi', 500);
            $table->unsignedTinyInteger('tong_so_tang')->default(1);
            $table->text('mo_ta')->nullable();
            $table->text('ban_do_iframe')->nullable(); // Tích hợp Google Maps
            $table->boolean('trang_thai')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nha_tro');
    }
};
