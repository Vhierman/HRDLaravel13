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
        Schema::create('history_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employees_id')->constrained('employees');
            $table->string('nik_karyawan');
            $table->foreignId('companies_id_history')->constrained('companies');
            $table->foreignId('areas_id_history')->constrained('areas');
            $table->foreignId('divisions_id_history')->constrained('divisions');
            $table->foreignId('positions_id_history')->constrained('positions');
            $table->date('tanggal_mutasi');
            $table->text('file_surat_mutasi');
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
        Schema::dropIfExists('history_positions');
    }
};
