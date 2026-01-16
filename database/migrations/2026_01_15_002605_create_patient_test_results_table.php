<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patient_test_results', function (Blueprint $table) {
            $table->id();
            $table->string('patien_id');
            $table->integer('test_id');
            $table->string('resilt');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_test_results');
    }
};
