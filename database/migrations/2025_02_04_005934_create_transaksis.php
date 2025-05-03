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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->datetime('tanggal');
            $table->foreignId('id_stan')->constrained('stans')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_siswa')->constrained('siswas')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('status', ['belum dikonfirmasi', 'dimasak', 'diantar', 'selesai']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
