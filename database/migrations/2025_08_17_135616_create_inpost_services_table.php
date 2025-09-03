<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inpost_services', function(Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('label')->nullable();
            $table->string('label_description')->nullable();
            $table->string('description');
            $table->boolean('active');
            $table->integer('position')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inpost_services');
    }
};
