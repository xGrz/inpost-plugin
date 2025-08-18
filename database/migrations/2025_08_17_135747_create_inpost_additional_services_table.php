<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inpost_additional_services', function(Blueprint $table) {
            $table->id();
            $table->string('inpost_service_id');
            $table->string('ident');
            $table->string('name');
            $table->string('description');
            $table->boolean('active');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('inpost_service_id')->references('id')->on('inpost_services');
        });
    }

    public function down(): void
    {
        Schema::table('inpost_additional_services', function(Blueprint $table) {
            $table->dropForeign(['inpost_service_id']);
        });
        Schema::dropIfExists('inpost_additional_services');
    }
};
