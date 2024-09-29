<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('peminjaman_alats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_lab')->constrained('labs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_alat')->constrained('alat_labs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_peminjam')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('jumlah_alat');
            $table->dateTime('confirm_time')->nullable();
            $table->dateTime('return_time')->nullable();
            $table->text('keterangan');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman_alats');
    }
};
