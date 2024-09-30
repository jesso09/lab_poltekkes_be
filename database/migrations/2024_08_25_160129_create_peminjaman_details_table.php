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
        Schema::create('peminjaman_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_peminjam')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('nama_lab');
            $table->string('nama_alat');
            $table->integer('jumlah_alat');
            $table->string('nama_peminjam');
            $table->string('role_peminjam');
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
        Schema::dropIfExists('peminjaman_details');
    }
};
