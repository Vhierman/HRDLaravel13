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
        Schema::create('certification_ministries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employees_id')->constrained('employees');
            $table->string('nik_karyawan');
            $table->string('jumlah_sertifikat_kementrian');
            $table->string('nomor_sertifikat_kementrian');
            $table->string('jenis_sertifikat_kementrian');
            $table->string('masa_berlaku_sertifikat_kementrian');
            $table->date('tanggal_terbit_kementrian');
            $table->date('sampai_tanggal_kementrian');
            $table->string('lsp_kementrian');
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
        Schema::dropIfExists('certification_ministries');
    }
};
