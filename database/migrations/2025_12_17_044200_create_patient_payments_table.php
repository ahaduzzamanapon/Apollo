<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('patient_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable(); 
            $table->foreignId('patient_report_id')->constrained('patient_reports')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->default('Cash'); // Cash, Card, Mobile Banking
            $table->string('transaction_id')->nullable();
            $table->string('remarks')->nullable();
            $table->foreignId('collected_by')->nullable()->constrained('admins')->onUpdate('cascade')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_payments');
    }
};
