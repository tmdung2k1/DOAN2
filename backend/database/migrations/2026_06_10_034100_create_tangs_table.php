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
        Schema::create('tang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nha_tro_id')->constrained('nha_tro')->onDelete('cascade');
            $table->unsignedTinyInteger('so_tang');
            $table->string('ten_tang', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::dropIfExists('tang');
    }
};
