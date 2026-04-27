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
        Schema::create('certification_bnsps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employees_id')->constrained('employees');
            $table->string('nik_karyawan');
            $table->string('jumlah_sertifikat_bnsp');
            $table->string('nomor_sertifikat_bnsp');
            $table->string('jenis_sertifikat_bnsp');
            $table->string('masa_berlaku_sertifikat_bnsp');
            $table->date('tanggal_terbit_bnsp');
            $table->date('sampai_tanggal_bnsp');
            $table->string('lsp_bnsp');
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
        Schema::dropIfExists('certification_bnsps');
    }
};
