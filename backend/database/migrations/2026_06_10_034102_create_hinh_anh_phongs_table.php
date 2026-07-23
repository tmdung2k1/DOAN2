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
        Schema::create('hinh_anh_phong', function (Blueprint $table) {
            $table->id('Ma_HinhAnhPhong');
            $table->unsignedBigInteger('Ma_Phong');
            $table->foreign('Ma_Phong')->references('Ma_Phong')->on('phong')->onDelete('cascade');
            $table->string('url_anh', 500);
            $table->boolean('la_anh_chinh')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hinh_anh_phong');
    }
};
