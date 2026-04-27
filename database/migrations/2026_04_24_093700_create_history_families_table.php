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
        Schema::create('history_families', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employees_id')->constrained('employees');
            $table->string('nik_karyawan');
            $table->enum('hubungan_keluarga',['Suami','Istri','Anak']);
            $table->string('nik_history_keluarga');
            $table->string('nomor_bpjs_kesehatan_history_keluarga');
            $table->string('nama_history_keluarga');
            $table->enum('jenis_kelamin_history_keluarga',['Pria','Wanita']);
            $table->string('tempat_lahir_history_keluarga');
            $table->date('tanggal_lahir_history_keluarga');
            $table->enum('golongan_darah_history_keluarga',['A','B','AB','O']);
            $table->text('dokumen_history_keluarga');
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
        Schema::dropIfExists('history_families');
    }
};
