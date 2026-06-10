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
        Schema::create('cai_dat', function (Blueprint $table) {
            $table->id();
            $table->string('khoa_cai_dat', 100)->unique(); // Ví dụ: gia_dien, ngon_ngu_mac_dinh
            $table->text('gia_tri');
            $table->string('mo_ta', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cai_dat');
    }
};
