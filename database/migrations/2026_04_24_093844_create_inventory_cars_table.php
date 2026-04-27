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
        Schema::create('inventory_cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employees_id')->constrained('employees');
            $table->string('nik_karyawan');
            $table->string('merk_mobil');
            $table->string('type_mobil');
            $table->string('nomor_polisi');
            $table->string('warna_mobil');
            $table->string('nomor_rangka_mobil');
            $table->string('nomor_mesin_mobil');
            $table->date('tanggal_akhir_pajak_mobil');
            $table->date('tanggal_akhir_plat_mobil');
            $table->date('tanggal_penyerahan_mobil');
            $table->text('foto_stnk_mobil');
            $table->text('foto_mobil');
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
        Schema::dropIfExists('inventory_cars');
    }
};
