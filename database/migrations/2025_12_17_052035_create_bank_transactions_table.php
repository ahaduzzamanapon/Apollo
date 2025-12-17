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
        Schema::dropIfExists('bank_transactions');
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained('banks')->onDelete('cascade');
            $table->enum('trans_type', ['Deposit', 'Withdraw']);
            $table->decimal('amount', 15, 2);
            $table->date('trans_date');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
