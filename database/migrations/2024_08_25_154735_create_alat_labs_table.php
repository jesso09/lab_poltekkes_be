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
        Schema::create('alat_labs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_lab')->constrained('labs')->onUpdate('cascade')->onDelete('cascade');
            $table->string('foto_alat')->nullable();
            $table->string('nama_alat');
            $table->integer('jumlah');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alat_labs');
    }
};
