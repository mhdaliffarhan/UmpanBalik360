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
        Schema::create('anggota_tim_kerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tim_kerja_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->enum('role', ['admin', 'anggota']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota_tim_kerjas');
    }
};
