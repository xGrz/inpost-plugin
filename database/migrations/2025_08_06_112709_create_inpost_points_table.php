<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inpost_points', function(Blueprint $table) {
            $table->id();
            $table->string('image_url');
            $table->string('name')->unique();
            $table->string('status', 20);
            $table->decimal('latitude', 11, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('location_type')->nullable();
            $table->string('location_description')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('post_code')->nullable();
            $table->boolean('payment_available');
            $table->string('payment_point_description')->nullable();
            $table->json('payment_type');
            $table->json('functions');
            $table->boolean('location_247');
            $table->integer('partner_id')->nullable();
            $table->string('physical_type_mapped')->nullable();
            $table->string('physical_type_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inpost_points');
    }
};
