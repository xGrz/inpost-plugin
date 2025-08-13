<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('parcel_templates', function(Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedInteger('width');
            $table->unsignedInteger('length');
            $table->unsignedInteger('height');
            $table->decimal('weight', 5, 1)->nullable();
            $table->boolean('non_standard')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcel_templates');
    }
};
