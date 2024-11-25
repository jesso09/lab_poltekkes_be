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
        Schema::create('pemakaian_labs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_lab')->constrained('labs')->onUpdate('cascade')->onDelete('cascade');
            $table->dateTime('tanggal_pemakaian');
            $table->dateTime('confirm_time')->nullable();
            $table->dateTime('jam_mulai');
            $table->dateTime('jam_selesai');
            $table->string('kegiatan');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemakaian_labs');
    }
};
