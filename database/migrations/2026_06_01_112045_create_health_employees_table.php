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
        Schema::create('health_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employees_id')->constrained('employees');
            $table->foreignId('faskes_id')->constrained('faskes');
            $table->string('nomor_mcu', 50)->nullable();
            $table->string('nik_karyawan', 16);
            $table->date('tanggal_pemeriksaan');
            $table->string('nama_faskes',100)->nullable();
            $table->string('dokter_pemeriksa',50)->nullable();
            $table->string('berat_badan',10)->nullable();
            $table->string('tinggi_badan',10)->nullable();
            $table->string('tekanan_darah',30)->nullable();
            $table->string('gula_darah',30)->nullable();
            $table->string('ekg',30)->nullable();
            $table->enum('jenis_pemeriksaan', ['Awal','Berkala','Khusus']);
            $table->enum('status_kelayakan', ['Fit','Fit With Note', 'Unfit']);
            $table->text('catatan_dokter')->nullable();
            $table->date('tanggal_pemeriksaan_berikutnya')->nullable();
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
        Schema::dropIfExists('health_employees');
    }
};
