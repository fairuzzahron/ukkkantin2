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
        Schema::create('stans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_stan', 100);
            $table->string('nama_pemilik', 100);
            $table->string('telp', 20);
            $table->foreignId('id_user')->constrained('users')->onUpdate('cascade')->onDelete('cascade');    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stans');
    }
};
