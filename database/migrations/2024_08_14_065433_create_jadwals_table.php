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
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_lab')->constrained('labs')->onUpdate('cascade')->onDelete('cascade');
            $table->string('hari');
            $table->dateTime('mulai');
            $table->dateTime('selesai');
            $table->string('praktikan');
            $table->string('semester');
            $table->string('mata_kuliah');
            $table->string('plp');
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
