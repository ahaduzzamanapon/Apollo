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
        Schema::table('patient_reports', function (Blueprint $table) {
            $table->boolean('ref_by_someone')->default(0)->after('reference_doctor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_reports', function (Blueprint $table) {
            $table->dropColumn('ref_by_someone');
        });
    }
};
