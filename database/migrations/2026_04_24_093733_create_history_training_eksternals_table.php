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
        Schema::create('history_training_eksternals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employees_id')->constrained('employees');
            $table->string('nik_karyawan');
            $table->string('institusi_penyelenggara_training_eksternal');
            $table->string('perihal_training_eksternal');
            $table->string('hari_awal_training_eksternal');
            $table->string('hari_akhir_training_eksternal');
            $table->date('tanggal_awal_training_eksternal');
            $table->date('tanggal_akhir_training_eksternal');
            $table->time('jam_training_eksternal');
            $table->string('lokasi_training_eksternal');
            $table->text('alamat_training_eksternal');
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
        Schema::dropIfExists('history_training_eksternals');
    }
};
