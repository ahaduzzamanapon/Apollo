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
            $table->integer('daily_id')->nullable()->after('report_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_reports', function (Blueprint $table) {
            $table->dropColumn('daily_id');
        });
    }
};
