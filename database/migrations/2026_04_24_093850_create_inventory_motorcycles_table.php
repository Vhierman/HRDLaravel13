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
        Schema::create('inventory_motorcycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employees_id')->constrained('employees');
            $table->string('nik_karyawan');
            $table->string('merk_motor');
            $table->string('type_motor');
            $table->string('nomor_polisi');
            $table->string('warna_motor');
            $table->string('nomor_rangka_motor');
            $table->string('nomor_mesin_motor');
            $table->date('tanggal_akhir_pajak_motor');
            $table->date('tanggal_akhir_plat_motor');
            $table->date('tanggal_penyerahan_motor');
            $table->text('foto_stnk_motor');
            $table->text('foto_motor');
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
        Schema::dropIfExists('inventory_motorcycles');
    }
};
