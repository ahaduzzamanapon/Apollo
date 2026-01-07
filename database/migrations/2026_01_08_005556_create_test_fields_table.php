<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('test_fields', function (Blueprint $table) {
            $table->id();
            $table->integer('test_id');
            $table->string('perameter');
            $table->string('unit');
            $table->string('ref_val');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_fields');
    }
};
