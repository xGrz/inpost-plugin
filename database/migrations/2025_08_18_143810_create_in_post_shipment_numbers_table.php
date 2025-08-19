<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inpost_shipment_numbers', function(Blueprint $table) {
            $table->string('inpost_ident')->primary();
            $table->string('tracking_number')->nullable();
            $table->string('return_tracking_number')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inpost_shipment_numbers');
    }
};
