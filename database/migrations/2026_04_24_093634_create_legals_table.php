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
        Schema::create('legals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perijinan');
            $table->string('nomor_perijinan');
            $table->string('instansi_penerbit');
            $table->date('tanggal_berlaku');
            $table->date('tanggal_habis');
            $table->string('masa_berlaku');
            $table->string('input_oleh')->nullable();
            $table->string('edit_oleh')->nullable();
            $table->string('hapus_oleh')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legals');
    }
};
