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
        Schema::create('tien_ich_phong', function (Blueprint $table) {
            $table->id('Ma_TienIchPhong');
            $table->unsignedBigInteger('Ma_Phong');
            $table->foreign('Ma_Phong')->references('Ma_Phong')->on('phong')->onDelete('cascade');
            $table->unsignedBigInteger('Ma_TienIch');
            $table->foreign('Ma_TienIch')->references('Ma_TienIch')->on('tien_ich')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tien_ich_phong');
    }
};
