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
        Schema::create('log_penilaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilaian_id')->constrained();
            $table->unsignedBigInteger('penilai_id');
            $table->unsignedBigInteger('dinilai_id');
            $table->foreign('penilai_id')->references('id')->on('users');
            $table->foreign('dinilai_id')->references('id')->on('users');
            $table->enum('role_penilai', ['atasan', 'sebaya', 'bawahan', 'diri sendiri']);
            $table->enum('status', ['belum', 'sudah'])->default('belum')->required();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_penilaians');
    }
};
