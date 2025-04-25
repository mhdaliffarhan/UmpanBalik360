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
        Schema::create('jabatan_strukturs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('struktur_id')->constrained();
            $table->string('nama_jabatan');
            $table->unsignedBigInteger('atasan')->nullable();
            $table->foreign('atasan')->references('id')->on('jabatan_strukturs')->onDelete('set null');
            $table->integer('level');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatan_strutkurs');
    }
};
