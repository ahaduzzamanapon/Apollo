<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('center_details', function (Blueprint $table) {
            $table->id();
            $table->string('name_bn');
            $table->string('name_en');
            $table->string('about');
            $table->string('address');
            $table->integer('phone');
            $table->string('logo_image');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('center_details');
    }
};
