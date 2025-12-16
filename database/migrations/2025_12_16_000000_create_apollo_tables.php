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
        // 1. Doctors Table
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

         // 2. Report Categories & Test Names
        Schema::create('report_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name'); // e.g., Haematology, Immunology
            $table->string('test_name'); // e.g., ECHO 2D & M-MODE
            $table->decimal('price', 10, 2);
            $table->string('room_no')->nullable(); // From Lab Room Setting
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Doctor Honorariums (Commission Settings)
        Schema::create('doctor_honorariums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->foreignId('report_category_id')->constrained('report_categories')->onDelete('cascade');
            
            // Commission can be fixed amount or percentage
            $table->decimal('amount', 10, 2)->nullable(); // Fixed Amount
            $table->decimal('percentage', 5, 2)->nullable(); // Percentage (e.g., 15.00)
            
            $table->timestamps();
        });

        // 4. Patients (Demographics)
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nid')->nullable();
            $table->string('mobile');
            $table->integer('age');
            $table->date('dob')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->timestamps();
        });

        // 5. Patient Reports (The Billing/Visit Record)
        Schema::create('patient_reports', function (Blueprint $table) {
            $table->id();
            // Custom ID like ADDC_000001 can be handled in logic or invalid here, but standard ID is safer for relations
            $table->string('report_code')->unique(); // ADDC_000001
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('reference_doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
            $table->date('report_date');
            
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('due_amount', 10, 2)->default(0);
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. Patient Tests (Pivot/Detail table for Patient Report)
        Schema::create('patient_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_report_id')->constrained('patient_reports')->onDelete('cascade');
            $table->foreignId('report_category_id')->constrained('report_categories');
            
            $table->decimal('price', 10, 2); // Price at the time of billing
            
            // Commission calculated at time of billing
            $table->decimal('commission_amount', 10, 2)->default(0); 
            
            $table->timestamps();
        });

        // 7. Accounts Ledgers (Settings)
        Schema::create('accounts_ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Flat Fee, Logistic Fee, etc.
            $table->enum('type', ['Income', 'Expense']);
            $table->timestamps();
        });

        // 8. Expenses
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('ledger_id')->constrained('accounts_ledgers');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->timestamps();
            $table->softDeletes();
        });

         // 9. Bank Transactions
         Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('bank_name'); // UCB
            $table->string('account_no');
            $table->enum('type', ['Deposit', 'Withdraw']);
            $table->decimal('amount', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('accounts_ledgers');
        Schema::dropIfExists('patient_tests');
        Schema::dropIfExists('patient_reports');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('doctor_honorariums');
        Schema::dropIfExists('report_categories');
        Schema::dropIfExists('doctors');
    }
};
