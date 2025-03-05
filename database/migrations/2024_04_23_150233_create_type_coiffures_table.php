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
        Schema::create('type_coiffures', function (Blueprint $table) {
            $table->uuid('id_hairstyle_type')->primary();
            $table->timestamps();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('category')->nullable();
            $table->string('image')->nullable();
            $table->string('image1')->nullable();
            $table->string('image2')->nullable();
            $table->double('price')->nullable();
            $table->uuid('salon_id');
            $table->foreign('salon_id')->references('id_salon')->on('salons')->onDelete('cascade');
            $table->uuid('coiffure_id');
            $table->foreign('coiffure_id')->references('id_coiffure')->on('coiffures')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_coiffures');
    }
};
