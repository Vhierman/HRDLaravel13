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
        Schema::create('overtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employees_id')->constrained('employees');
            $table->string('nik_karyawan');
            $table->string('jam_masuk');
            $table->string('jam_istirahat');
            $table->string('jam_pulang');
            $table->string('keterangan_lembur');
            $table->date('tanggal_lembur');
            $table->string('jenis_lembur');
            $table->string('jam_lembur');
            $table->string('jam_pertama');
            $table->string('jumlah_jam_pertama');
            $table->string('jam_kedua');
            $table->string('jumlah_jam_kedua');
            $table->string('jam_ketiga');
            $table->string('jumlah_jam_ketiga');
            $table->string('jam_keempat');
            $table->string('jumlah_jam_keempat');
            $table->string('uang_makan_lembur');
            $table->string('acc_hrd');
            $table->string('waktu_acc_hrd');
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
        Schema::dropIfExists('overtimes');
    }
};
