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
        Schema::table('patient_tests', function (Blueprint $table) {
            $table->string('approval_status')->default('pending')->after('commission_amount'); // pending, approved
            $table->decimal('approved_amount', 10, 2)->nullable()->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('approved_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_tests', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'approved_amount', 'approved_at']);
        });
    }
};
