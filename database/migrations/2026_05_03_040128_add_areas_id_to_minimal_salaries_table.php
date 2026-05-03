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
        Schema::table('minimal_salaries', function (Blueprint $table) {
            //
            $table->foreignId('areas_id')->constrained('areas')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('minimal_salaries', function (Blueprint $table) {
            //
            $table->dropColumn('areas_id');
        });
    }
};
