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
        Schema::create('employees_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employees_id')->constrained('employees');
            $table->foreignId('companies_id')->constrained('companies');
            $table->foreignId('golongans_id')->constrained('golongans');
            $table->foreignId('areas_id')->constrained('areas');
            $table->foreignId('divisions_id')->constrained('divisions');
            $table->foreignId('positions_id')->constrained('positions');
            $table->string('nik_karyawan_keluar');
            $table->string('nama_karyawan_keluar');
            $table->string('nomor_npwp_karyawan_keluar');
            $table->string('email_karyawan_keluar');
            $table->string('nomor_handphone_karyawan_keluar');
            $table->string('tempat_lahir_karyawan_keluar');
            $table->date('tanggal_lahir_karyawan_keluar');
            $table->string('nomor_bpjsketenagakerjaan_karyawan_keluar');
            $table->string('nomor_bpjskesehatan_karyawan_keluar');
            $table->string('nomor_rekening_karyawan_keluar');
            $table->enum('pendidikan_terakhir_karyawan_keluar',['SD','SMP','SMA/SMK','D3','S1','S2','S3']);
            $table->enum('jenis_kelamin_karyawan_keluar',['Pria','Wanita']);
            $table->enum('agama_karyawan_keluar',['Islam','Kristen Protestan','Kristen Katholik','Hindu','Budha']);
            $table->string('alamat_karyawan_keluar');
            $table->string('rt_karyawan_keluar');
            $table->string('rw_karyawan_keluar');
            $table->string('kelurahan_karyawan_keluar');
            $table->string('kecamatan_karyawan_keluar');
            $table->string('kota_karyawan_keluar');
            $table->string('provinsi_karyawan_keluar');
            $table->string('kode_pos_karyawan_keluar');
            $table->string('nomor_absen_karyawan_keluar');
            $table->enum('golongan_darah_karyawan_keluar',['A','B','AB','O']);
            $table->string('nomor_kartu_keluarga_karyawan_keluar');
            $table->enum('status_nikah_karyawan_keluar',['Single','Menikah','Janda','Duda']);
            $table->string('nama_ayah_karyawan_keluar');
            $table->string('nama_ibu_karyawan_keluar');
            $table->date('tanggal_masuk_karyawan_keluar');
            $table->date('tanggal_keluar_karyawan_keluar');
            $table->enum('status_kerja_karyawan_keluar',['PKWTT','PKWT','Harian','Outsourcing']);
            $table->string('keterangan_keluar');
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
        Schema::dropIfExists('employees_outs');
    }
};
