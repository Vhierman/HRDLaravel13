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
        Schema::create('history_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employees_id')->constrained('employees');
            $table->string('nik_karyawan');
            $table->string('gaji_pokok');
            $table->string('uang_makan');
            $table->string('uang_transport');
            $table->string('tunjangan_tugas');
            $table->string('tunjangan_pulsa');
            $table->string('tunjangan_jabatan');
            $table->string('jumlah_upah');
            $table->string('upah_lembur_perjam');
            $table->string('potongan_bpjsks_perusahaan');
            $table->string('potongan_jht_perusahaan');
            $table->string('potongan_jp_perusahaan');
            $table->string('potongan_jkm_perusahaan');
            $table->string('potongan_jkk_perusahaan');
            $table->string('jumlah_bpjstk_perusahaan');
            $table->string('potongan_bpjsks_karyawan');
            $table->string('potongan_jht_karyawan');
            $table->string('potongan_jp_karyawan');
            $table->string('jumlah_bpjstk_karyawan');
            $table->string('take_home_pay');
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
        Schema::dropIfExists('history_salaries');
    }
};
