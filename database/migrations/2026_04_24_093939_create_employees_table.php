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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('golongans_id')->constrained('golongans');
            $table->foreignId('companies_id')->constrained('companies');
            $table->foreignId('areas_id')->constrained('areas');
            $table->foreignId('divisions_id')->constrained('divisions');
            $table->foreignId('positions_id')->constrained('positions');
            $table->foreignId('working_hours_id')->constrained('working_hours');
            $table->string('nik_karyawan');
            $table->string('nama_karyawan');
            $table->string('email_karyawan');
            $table->string('nomor_absen');
            $table->string('nomor_npwp');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('agama',['Islam','Kristen Protestan','Kristen Katholik','Hindu','Budha']);
            $table->enum('jenis_kelamin',['Pria','Wanita']);
            $table->enum('pendidikan_terakhir',['SD','SMP','SMA/SMK','D3','S1','S2','S3']);
            $table->string('nomor_handphone');
            $table->enum('golongan_darah',['A','B','AB','O']);
            $table->string('alamat');
            $table->string('rt');
            $table->string('rw');
            $table->string('kelurahan');
            $table->string('kecamatan');
            $table->string('kota');
            $table->string('provinsi');
            $table->string('kode_pos');
            $table->text('foto_karyawan');
            $table->text('foto_ktp');
            $table->text('foto_npwp');
            $table->text('foto_kk');
            $table->string('nomor_bpjskesehatan');
            $table->string('nomor_bpjsketenagakerjaan');
            $table->string('nomor_kartu_keluarga');
            $table->enum('status_nikah',['Single','Menikah','Janda','Duda']);
            $table->string('nama_ibu');
            $table->string('nama_ayah');
            $table->date('tanggal_mulai_kerja');
            $table->date('tanggal_akhir_kerja');
            $table->enum('status_kerja',['PKWTT','PKWT','Harian','Outsourcing']);
            $table->string('nama_bank');
            $table->integer('nomor_rekening');
            $table->string('note_lembur');
            $table->string('keterangan');
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
        Schema::dropIfExists('employees');
    }
};
