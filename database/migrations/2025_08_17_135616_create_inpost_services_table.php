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
            $table->string('description');
            $table->boolean('active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inpost_services');
    }
};
